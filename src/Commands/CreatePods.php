<?php namespace Jake142\LaravelPods\Commands;

use Illuminate\Console\Command;
use Jake142\LaravelPods\Composer;
use Illuminate\Filesystem\Filesystem;
use Jake142\LaravelPods\Commands\Generators\JobGenerator;
use Jake142\LaravelPods\Commands\Generators\PodGenerator;
use Jake142\LaravelPods\Commands\Generators\TestGenerator;
use Jake142\LaravelPods\Commands\Generators\ComposerGenerator;
use Jake142\LaravelPods\Commands\Generators\ControllerGenerator;
use Jake142\LaravelPods\Commands\Generators\ServiceProviderGenerator;
use Jake142\LaravelPods\Commands\Generators\RouteServiceProviderGenerator;

class CreatePods extends Command
{
    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Create a new pod';

    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'pods:create';

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
        try
        {
            $name    = $this->ask('Name your pod');
            $version = $this->ask('Name your version eg. V1');
            $this->createPod($name, $version);

            //Create mandatory stuff
            $this->createComposer($name, $version);
            $this->createServiceProvider($name, $version);
            $this->createRouteServiceProvider($name, $version);

            //Create optional stuff
            $createController = $this->choice('Would you like to create a controller? (recommended)', ['Yes', 'No'], 0);

            if ('Yes' == $createController) {
                $this->createController($name, $version);
            } else {
                $this->emptyRoutes($name, $version);
            }

            $createJob = $this->choice('Would you like to create a Job? (recommended)', ['Yes', 'No'], 0);

            if ('Yes' == $createJob) {
                $this->createJob($name, $version);
            }

            $createTest = $this->choice('Would you like to create a Test? (recommended)', ['Yes', 'No'], 0);

            if ('Yes' == $createTest) {
                $this->createTest($name, $version);
            }

            $this->info('The Pod '.$name.' is ready to go. Fill it with models, middlewares and loads of love!');

            $composer->addPod($version, $name);

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Create the composer file
     *
     * @return void
     */
    private function createComposer($name, $version)
    {
        (new ComposerGenerator($name, $version))->run();
    }

    /**
     * Create the controller
     *
     * @return void
     */
    private function createController($name, $version)
    {
        (new ControllerGenerator($name, $version))->run();
    }

    /**
     * Create the job
     *
     * @return void
     */
    private function createJob($name, $version)
    {
        (new JobGenerator($name, $version))->run();
    }

    /**
     * Create the pod
     *
     * @return void
     */
    private function createPod($name, $version)
    {
        (new PodGenerator($name, $version))->exist()->run();
    }

    /**
     * Create the RouteServiceProvider file
     *
     * @return void
     */
    private function createRouteServiceProvider($name, $version)
    {
        (new RouteServiceProviderGenerator($name, $version))->run();
    }

    /**
     * Create the ServiceProvider file
     *
     * @return void
     */
    private function createServiceProvider($name, $version)
    {
        (new ServiceProviderGenerator($name, $version))->run();
    }

    /**
     * Create the test
     *
     * @return void
     */
    private function createTest($name, $version)
    {
        (new TestGenerator($name, $version))->run();
    }

    /**
     * Create the empty routes
     *
     * @return void
     */
    private function emptyRoutes($name, $version)
    {
        $this->filesystem->put(base_path().'/pods/'.$version.'/'.$name.'/api_routes.stub', '//Your routes goes here');
        $this->filesystem->put(base_path().'/pods/'.$version.'/'.$name.'/web_routes.stub', '//Your routes goes here');
    }
}
