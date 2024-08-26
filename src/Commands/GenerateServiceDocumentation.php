<?php namespace Jake142\Service\Commands;

use Storage;
use Jake142\Service\Composer;
use Illuminate\Console\Command;
use OpenApi\Generator;
use OpenApi\Analysis;
use OpenApi\Annotations\OpenApi;

class GenerateServiceDocumentation extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate application OpenAPI documentation';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-service:generate-docs {service=all} {constants?} {--workaround-readme}';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Composer $composer)
    {
        $service = strtolower($this->argument('service'));
        if ('all' != $service) {
            if (!$composer->serviceExist($service)) {
                throw new \Exception('Service do not exist');
            }

            if (!$composer->serviceEnabled($service)) {
                throw new \Exception('Enable service before generating docs');
            }

            $url = $composer->getUrl($service);

            $appDir = base_path($url);
            $docDir = $service.'/docs';
        } else {
            $appDir = base_path('Services');
            $docDir = 'laravel-service/docs';
        }

        $this->info('Generating docs for service '.$service);
        if (Storage::exists($docDir)) {
            Storage::deleteDirectory($docDir);
        }

        Storage::makeDirectory($docDir);


        // Create a new instance of the Generator class
        $generator = new \OpenApi\Generator();

        // Set the analyzer to the legacy TokenAnalyser
        $generator->setAnalyser(new \OpenApi\Analysers\TokenAnalyser());

        // Scan the project directory using the generator instance
        $swagger = $generator->generate([$appDir]);

        $filePath = $docDir.'/swagger.json';

        if ($this->option('workaround-readme')) {
            Storage::put(
                $filePath,
                self::compileWorkaroundForReadme($swagger)
            );
        } else {
            $swagger->saveAs(storage_path('app/'.$filePath));
        }

        //Define constants if there are any
        if ($this->argument('constants')) {
            self::defineConstants(config($this->argument('constants')) ?: [], $filePath);
        }
        $this->info('Created docs for '.$service.' in '.$filePath);
    }

    protected static function compileWorkaroundForReadme($swaggerInstance): string
    {
        $swaggerJson = json_decode($swaggerInstance->toJson(), true);
        $combine     = [];
        $walkPaths   = function ($parent) use (&$walkPaths, &$combine) {
            $output = [];
            foreach ($parent as $key => $child) {
                if ('allOf' === $key) {
                    $componentCombination = collect($child)->map(
                        function ($item) {
                            return $item['$ref'];
                        }
                    );
                    $componentCombinationName = 'allOf'.$componentCombination->map(
                        function ($ref) {
                            return basename($ref);
                        }
                    )->implode('And');
                    $combine[$componentCombinationName] = $componentCombination->toArray();
                    $output['$ref']                     = '#/components/schemas/'.$componentCombinationName;
                } else if (gettype($child) === 'array' || gettype($child) === 'object') {
                    $output[$key] = $walkPaths($child);
                } else {
                    $output[$key] = $child;
                }
            }
            return $output;
        };
        $addCombinedSchemas = function ($swaggerSchemas) use (&$combine) {
            foreach ($combine as $key => $componentGroup) {
                foreach ($componentGroup as $ref) {
                    $swaggerSchemas[$key]['properties'] = array_merge(
                        $swaggerSchemas[$key]['properties'] ?? [],
                        $swaggerSchemas[basename($ref)]['properties']
                    );
                }
            }
            return $swaggerSchemas;
        };
        $swaggerJson['paths'] = $walkPaths(
            $swaggerJson['paths']
        );
        $swaggerJson['components']['schemas'] = $addCombinedSchemas(
            $swaggerJson['components']['schemas']
        );
        return json_encode($swaggerJson);
    }

    protected static function defineConstants(array $constants, string $filePath)
    {
        if (!empty($constants)) {
            $swaggerFile = Storage::get($filePath);
            foreach ($constants as $key => $value) {
                $swaggerFile = str_replace($key, $value, $swaggerFile);
            }
            Storage::put(
                $filePath,
                $swaggerFile
            );
        }
    }
}