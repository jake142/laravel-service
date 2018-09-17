<?php namespace Jake142\Service\Commands;

use Illuminate\Console\Command;
use Artisan;

class RunService extends Command
{
    /**
     * The signature of command.
     *
     * @var string
     */
    protected $signature = 'service:run';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Run queues for all active servers';

    /**
     * Execute the command.
     *
     * @see fire()
     * @return void
     */
    public function handle(){
        try
        {

            $services = config('appservices');
            if(!empty($services)) {
                $queues = '';
                //Setup queue high
                foreach($services as $key=>$value) {
                    if($value==1) {
                        $queues = $queues . $key . '*high,';
                    }
                }
                //Setup queue medium
                foreach($services as $key=>$value) {
                    if($value==1) {
                        $queues = $queues . $key . '*medium,';
                    }
                }
                //Setup queue low
                foreach($services as $key=>$value) {
                    if($value==1) {
                        $queues = $queues . $key . '*low,';
                    }
                }
                if($queues=='')
                    throw new \Exception('There is no active services. Run php artisan service:create to create a service.');
                $queues = rtrim($queues,',');
                $serviceRegistered = Artisan::call('queue:work', [
                    '--queue' => $queues
                ]);
            } else {
                throw new \Exception('There is no active services. Run php artisan service:create to create a service.');
            }

        }
        catch(\Exception $e)
        {
            $this->error($e->getMessage());
        }
    }
}
