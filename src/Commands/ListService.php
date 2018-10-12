<?php namespace Jake142\Service\Commands;

use Illuminate\Console\Command;
use Jake142\Service\Composer;

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
    public function handle(Composer $composer){
        try
        {
            $services = $composer->listServices();
            if(empty($services)) {
                $this->error('You have no services created. Run php artisan service:create'); 
            }
            else {
                foreach($services as $key => $value) {
                    if($value=='ENABLED') {
                        $this->info($key . ' status:' . $value);                
                    } else {
                        $this->error($key . ' status:' . $value); 
                    }
                }
            }


        }
        catch(\Exception $e)
        {
            $this->error($e->getMessage());
        }
    }
}
