<?php namespace Jake142\LaravelPods\Commands;

use Illuminate\Console\Command;
use Jake142\LaravelPods\Composer;
use Illuminate\Filesystem\Filesystem;

class SetupPods extends Command
{
    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Setup project for laravel-pods';

    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'pods:setup';

    /**
     * The signature of command.
     *
     * @var string
     */
    protected $signature = 'pods:setup {--scaffold} {--migrate-services}';

    public function __construct()
    {
        $this->filesystem = new Filesystem;
        parent::__construct();
    }

    /**
     * Execute the command.
     *
     * @see fire()
     * @return void
     */
    public function handle(Composer $composer)
    {
        // Quick and dirty, should cover basic use cases
        try
        {
            // Create the pods directory if it doesn't exist
            $this->mkBaseDir('pods');

            // Bail service migration early if not applicable
            if ($this->option('migrate-services') && !file_exists(base_path('Services'))) {
                return $this->error('This migration is only applicable to migrate laravel-services v0.0.2 projects.');
            }

            // The "--scaffold" flag will arrange an vanilla laravel project into our
            // optionated structure. The "--migrate-services" flag will migrate already
            // created "Services" (in v0.0.2) into this new structure
            if ($this->option('scaffold') || $this->option('migrate-services')) {
                // Bail if things doesn't look right, eg: things already exists
                if (file_exists(base_path('common')) || file_exists(base_path('pods'))) {
                    return $this->error('Setup already done!');
                }
                // We need to keep track of this not to overwrite anything
                $currentAppDirContainsTests   = file_exists(base_path('app/tests'));
                $currentAppDirContainsRoutes  = file_exists(base_path('app/routes'));
                $currentAppDirContainsViews   = file_exists(base_path('app/views'));
                $currentBaseDirContainsTests  = file_exists(base_path('tests'));
                $currentBaseDirContainsRoutes = file_exists(base_path('routes'));
                // Regardless of current setup we'll want an "common" folder
                $this->mkBaseDir('common');
                // If there's an "app" folder present we'll move its contents to "common"
                if (file_exists(base_path('app'))) {
                    // Remember if there's any tests in this folder
                    $this->moveDirContentsToCommon('app');
                    // If there was tests in this folder rename it proper
                    if ($currentAppDirContainsTests) {
                        $this->moveDirToCommon('common/tests', 'Tests');
                    } else {
                        // Else we'll move base dir tests there
                        $this->moveDirToCommon('tests', 'Tests');
                    }
                    // Update the testpath
                    if ($currentAppDirContainsTests || $currentBaseDirContainsTests) {
                        $this->replaceStringInFile('phpunit.xml', './tests/', './common/Tests/');
                        $this->replaceStringInFile('phpunit.xml', './app', './common');
                    }
                    // If there was routes in this folder rename it proper
                    if ($currentAppDirContainsRoutes) {
                        $this->moveDirToCommon('common/routes', 'Routes');
                    } else {
                        $this->moveDirToCommon('routes', 'Routes');
                    }
                    // If there was view in this folder rename it proper
                    if ($currentAppDirContainsViews) {
                        $this->moveDirToCommon('common/views', 'Views');
                    }
                    // If there's an Route service provider defined update it's contents
                    if (file_exists(base_path('common/Providers/RouteServiceProvider.php'))) {
                        // Cover both "/app/routes" and "/routes" cases
                        $this->replaceStringInFile('common/Providers/RouteServiceProvider.php', 'app/routes/web.php', 'common/Routes/web.php');
                        $this->replaceStringInFile('common/Providers/RouteServiceProvider.php', 'app/routes/api.php', 'common/Routes/api.php');
                        $this->replaceStringInFile('common/Providers/RouteServiceProvider.php', 'routes/web.php', 'common/Routes/web.php');
                        $this->replaceStringInFile('common/Providers/RouteServiceProvider.php', 'routes/api.php', 'common/Routes/api.php');
                    }
                }

                // Just make sure we dont overwrite tests
                if ($currentAppDirContainsTests && $currentBaseDirContainsTests) {
                    $this->warning('==> Warning!');
                    $this->warning('    Tests found in both "tests" and "app/tests" (now "common/Tests")!');
                    $this->warning('    * "tests" will not be autoloaded by composer anymore,');
                    $this->warning('       theese must be merged to "common/Tests".');
                }
                // Just make sure we dont overwrite routes
                if ($currentAppDirContainsRoutes && $currentBaseDirContainsRoutes) {
                    $this->warning('==> Warning!');
                    $this->warning('    Routes found in both "routes" and "app/routes" (now "common/Routes")!');
                    $this->warning('    * "routes" will not be autoloaded by composer anymore,');
                    $this->warning('       theese must be merged to "common/Routes".');
                }
                // Replace Console kernel path
                $this->replaceStringInFile('common/Console/Kernel.php', 'routes/console.php', 'common/Routes/console.php');

                // Load up composer
                $composerJson = $composer->readComposer();
                // Change the path of what was "app" into "common"
                foreach ($composerJson['autoload']['psr-4'] as $key => $value) {
                    if ('App\\' === $key) {
                        $composerJson['autoload']['psr-4'][$key] = 'common/';
                        // $composerJson['autoload']['psr-4']['Core\\'] = 'common/';
                    }
                }
                // Replace the autoload dev paths
                foreach ($composerJson['autoload-dev']['psr-4'] as $key => $value) {
                    if ('tests/' === $value) {
                        $composerJson['autoload-dev']['psr-4'][$key] = 'common/Tests/';
                    }
                }
                // And write it back
                $composer->writeComposer($composerJson);
            }

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function mkBaseDir($folder)
    {
        if (!file_exists(base_path($folder))) {
            mkdir(base_path($folder), 0777, true);
        }
    }

    public function moveDirContentsToCommon($folder)
    {
        if (file_exists(base_path($folder))) {
            $this->filesystem->moveDirectory(base_path($folder), base_path('common'), true);
        }
    }

    public function moveDirToCommon($folder, $rename = false)
    {
        if (file_exists(base_path($folder))) {
            $this->filesystem->moveDirectory(base_path($folder), base_path('common/'.($rename ?: $folder)), true);
        }
    }

    public function moveDirToCore($folder, $rename = false)
    {
        if (file_exists(base_path($folder))) {
            $this->filesystem->moveDirectory(base_path($folder), base_path('common/core/'.($rename ?: $folder)), true);
        }
    }

    public function replaceStringInFile($filePath, $find, $replace)
    {
        $path = base_path($filePath);
        if (file_exists($path)) {
            $contents = file_get_contents($path);
            $contents = str_replace($find, $replace, $contents);
            file_put_contents($path, $contents);
        }
    }
}
