<?php namespace Jake142\Service\Commands;

use Illuminate\Console\Command;
use Jake142\Service\Config;

class ListService extends Command
{
    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'service:list';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'List all services';

    /**
     * Execute the command.
     *
     * @see fire()
     * @return void
     */
    public function handle(){
        try
        {
            $services = (new Config())->readConfig();
            if(empty($services)) {
                $this->error('You have no services created. Run php artisan service:create'); 
            }
            else {
                foreach($services as $key => $value)
                    $this->info($key . ' status:' . ($value['status']==0 ? 'INACTIVE':'ACTIVE'));                
            }


        }
        catch(\Exception $e)
        {
            $this->error($e->getMessage());
        }
    }
}
