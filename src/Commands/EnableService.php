<?php namespace Jake142\Service\Commands;

use Illuminate\Console\Command;
use Jake142\Service\Composer;
use Jake142\Service\PhpunitXML;

class EnableService extends Command
{
    /**
     * The name of command.
     *
     * @var string
     */
    protected $signature = 'laravel-service:enable {service}';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Enable a service';

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
            if($composer->serviceEnabled($service))
                throw new \Exception('Service is already enabled');
            $this->info('Enabling '.$service.'...');
            $composer->enableService($service);
            $phpUnitXML->enableService($service);
            $this->info($service.' is now enabled and ready to be used');
        }
        catch(\Exception $e)
        {
            $this->error($e->getMessage());
        }
    }
}
