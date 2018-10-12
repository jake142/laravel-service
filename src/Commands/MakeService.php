<?php namespace Jake142\Service\Commands;

use Illuminate\Console\Command;
use Jake142\Service\Composer;
use Jake142\Service\Commands\Generators\ServiceGenerator;
use Jake142\Service\Commands\Generators\ComposerGenerator;
use Jake142\Service\Commands\Generators\ServiceProviderGenerator;
use Jake142\Service\Commands\Generators\RouteServiceProviderGenerator;
use Jake142\Service\Commands\Generators\ControllerGenerator;
use Jake142\Service\Commands\Generators\JobGenerator;
use Jake142\Service\Commands\Generators\TestGenerator;
use Illuminate\Filesystem\Filesystem;
use Artisan;

class MakeService extends Command
{
    public function __construct()
    {
        $this->filesystem = new Filesystem;
        parent::__construct();
    }
    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'service:make';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Make a new service';

    /**
     * Execute the command.
     *
     * @see fire()
     * @return void
     */
    public function handle(Composer $composer){
        try
        {
            $name = $this->ask('Name your service');
            $version = $this->ask('Name your version eg. V1');
            $this->createService($name, $version);

            //Create mandatory stuff
            $this->createComposer($name, $version);
            $this->createServiceProvider($name, $version);
            $this->createRouteServiceProvider($name, $version);

            //Create optional stuff
            $createController = $this->choice('Would you like to create a controller? (recommended)', ['Yes','No'], 0);

            if($createController=='Yes') {
                $this->createController($name, $version);
            } else {
                $this->emptyRoutes($name, $version);
            }

            $createJob = $this->choice('Would you like to create a Job? (recommended)', ['Yes','No'], 0);

            if($createJob=='Yes')
                $this->createJob($name, $version);

            $createTest = $this->choice('Would you like to create a Test? (recommended)', ['Yes','No'], 0);

            if($createTest=='Yes')
                $this->createTest($name, $version);

            $this->info('The service '.$name.' is ready to go. Fill it with models, middlewares and loads of love!');
            
            $composer->addService($version, $name);

        }
        catch(\Exception $e)
        {
            $this->error($e->getMessage());
        }
    }
    /**
     * Create the service
     *
     * @return void
     */
    private function createService($name, $version) {
        (new ServiceGenerator($name, $version))->exist()->run();
    }
    /**
     * Create the composer file
     *
     * @return void
     */
    private function createComposer($name, $version) {
        (new ComposerGenerator($name, $version))->run();
    }
    /**
     * Create the ServiceProvider file
     *
     * @return void
     */
    private function createServiceProvider($name, $version) {
        (new ServiceProviderGenerator($name, $version))->run();
    }
    /**
     * Create the RouteServiceProvider file
     *
     * @return void
     */
    private function createRouteServiceProvider($name, $version) {
        (new RouteServiceProviderGenerator($name, $version))->run();
    }
    /**
     * Create the empty routes
     *
     * @return void
     */
    private function emptyRoutes($name, $version) {
        $this->filesystem->put(base_path().'/Services/'.$version.'/'.$name.'/api_routes.stub', '//Your routes goes here');
        $this->filesystem->put(base_path().'/Services/'.$version.'/'.$name.'/web_routes.stub', '//Your routes goes here');
    }
    /**
     * Create the controller
     *
     * @return void
     */
    private function createController($name, $version) {
        (new ControllerGenerator($name, $version))->run();
    }
    /**
     * Create the job
     *
     * @return void
     */
    private function createJob($name, $version) {
        (new JobGenerator($name, $version))->run();
    }
    /**
     * Create the test
     *
     * @return void
     */
    private function createTest($name, $version) {
        (new TestGenerator($name, $version))->run();
    }
}
