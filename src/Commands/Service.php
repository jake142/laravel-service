<?php namespace Jake142\Service\Commands;

use Illuminate\Console\Command;
use Jake142\Service\Commands\Generators\ServiceGenerator;
use Jake142\Service\Commands\Generators\ControllerGenerator;
use Jake142\Service\Commands\Generators\JobGenerator;
use Illuminate\Filesystem\Filesystem;

class Service extends Command
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
    protected $name = 'service:create';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Create a new service';

    /**
     * Execute the command.
     *
     * @see fire()
     * @return void
     */
    public function handle(){
        try
        {
            $name = $this->ask('Name your service');
            $version = $this->ask('Name your version eg. V1');
            $this->createService($name, $version);

            $createController = $this->choice('Would you like to create a controller? (recommended)', ['No','Yes'], 0);
            if($createController===1) {
                $this->createController($name, $version);
            } else {
                $this->emptyRoutes($name, $version);
            }

            $createJob = $this->choice('Would you like to create a Job? (recommended)', ['No','Yes'], 0);

            if($createJob===1)
                $this->createJob($name, $version);

           $this->info('The service '.$name.' is ready to go. Fill it with models, tests, middlewares and loads of love');

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
     * Create the empty routes
     *
     * @return void
     */
    private function emptyRoutes($name, $version) {
        $this->filesystem->put(app_path().'/Services/'.$version.'/'.$name.'/routes/api.php', '//Your routes goes here');
        $this->filesystem->put(app_path().'/Services/'.$version.'/'.$name.'/routes/web.php', '//Your routes goes here');
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
}
