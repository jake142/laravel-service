<?php namespace Jake142\Service\Commands;

use Illuminate\Console\Command;
use Jake142\Service\Composer;
use Storage;

class GenerateServiceDocumentation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-service:generate-docs {service=all} {constants?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate application OpenAPI documentation';

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
        //Define constants if there are any
        if($this->argument('constants')) {
            self::defineConstants(config($this->argument('constants')) ?: []);
        }
        $service = strtolower($this->argument('service'));
        if($service != 'all') {
            if(!$composer->serviceExist($service))
                throw new \Exception('Service do not exist');
            if(!$composer->serviceEnabled($service))
                throw new \Exception('Enable service before generating docs');
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

        $swagger = \OpenApi\scan($appDir);
        $file = $docDir.'/swagger.json';
        $swagger->saveAs(storage_path('app/'.$file));
        $this->info('Created docs for '.$service.' in '.$file);
    }
    protected static function defineConstants(array $constants)
    {
        if (! empty($constants)) {
            foreach ($constants as $key => $value) {
                defined($key) || define($key, $value);
            }
        }
    }
}
