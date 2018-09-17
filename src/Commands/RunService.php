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
    protected $signature = 'service:run {--mode=}';

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
            //Set parameters
            if($this->option('mode')=='work' or $this->option('mode')=='listen') {
                $mode = $this->option('mode');
            }
            else {
                throw new \Exception('Parameter {mode} can only be work or listen');
            }
            $services = config('appservices');
            if(!empty($services)) {
                $queues = '';
                foreach($services as $key=>$value) {
                    if($value==1) {
                        $queues = $queues . $key . ',';
                    }
                }
                $queues = rtrim($queues,',');
                $serviceRegistered = Artisan::call('queue:'.$mode, [
                    '--queue' => $queues
                ]);
            } else {
                $this->error('There is no active services. Run php artisan service:create to create a service.');
            }

        }
        catch(\Exception $e)
        {
            $this->error($e->getMessage());
        }
    }
}
