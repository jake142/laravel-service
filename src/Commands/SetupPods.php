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
    protected $signature = 'pods:setup {--scaffold}';

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

            // Shall I scaffold the whole project?
            if ($this->option('scaffold') && file_exists(base_path('app'))) {
                // Create the "common" directory
                $this->mkBaseDir('common');
                // Move the contents of app to "common"
                $this->moveDirContentsToCommon('app');
                // Move theese directories to "common"
                // $this->moveDirToCommon('resources');
                $this->moveDirToCommon('routes', 'Routes');
                $this->moveDirToCommon('tests', 'Tests');
                // Change paths to things we've just moved
                $this->replaceStringInFile('phpunit.xml', './tests/', './common/Tests/');
                $this->replaceStringInFile('phpunit.xml', './app', './common');
                $this->replaceStringInFile('common/Console/Kernel.php', 'routes/console.php', 'common/Routes/console.php');
                $this->replaceStringInFile('common/Providers/RouteServiceProvider.php', 'routes/web.php', 'common/Routes/web.php');
                $this->replaceStringInFile('common/Providers/RouteServiceProvider.php', 'routes/api.php', 'common/Routes/api.php');
                $this->replaceStringInFile('common/Providers/BroadcastServiceProvider.php', 'routes/channels.php', 'common/Routes/channels.php');
                // Load current composer configuration
                $composerJson = $composer->readComposer();
                // Update autoload paths
                foreach ($composerJson['autoload']['psr-4'] as $key => $value) {
                    if ('App\\' === $key) {
                        $composerJson['autoload']['psr-4'][$key]     = 'common/';
                        $composerJson['autoload']['psr-4']['Core\\'] = 'common/';
                    }
                }
                // foreach ($composerJson['autoload']['classmap'] as $key => $value) {
                //     if (strpos($value, 'database') !== false) {
                //         $composerJson['autoload']['classmap'][$key] = 'common/'.$value;
                //     }
                // }
                foreach ($composerJson['autoload-dev']['psr-4'] as $key => $value) {
                    if ('tests/' === $value) {
                        $composerJson['autoload-dev']['psr-4'][$key] = 'common/Tests/';
                    }
                }
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
