<?php namespace Jake142\Service\Commands;

use Illuminate\Console\Command;
use Jake142\Service\Composer;
use Jake142\Service\PhpunitXML;

class DisableService extends Command
{
    /**
     * The name of command.
     *
     * @var string
     */
    protected $signature = 'service:disable {service}';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Disable a service';

    /**
     * Execute the command.
     *
     * @see fire()
     * @return void
     */
    public function handle(Composer $composer, PhpunitXML $phpUnitXML){
        try
        {
            $service = $this->argument('service');
            if(!$composer->serviceExist($service))
                throw new \Exception('Service do not exist');
            if(!$composer->serviceEnabled($service))
                throw new \Exception('Service is already disabled');
            $this->info('Disabling '.$service.'...');
            $composer->disableService($service);
            $phpUnitXML->disableService($service);
            $this->info($service.' is now disabled');
        }
        catch(\Exception $e)
        {
            $this->error($e->getMessage());
        }
    }
}
