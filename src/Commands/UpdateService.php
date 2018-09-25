<?php namespace Jake142\Service\Commands;

use Illuminate\Console\Command;
use Jake142\Service\Config;
use Jake142\Service\PhpunitXML;

class UpdateService extends Command
{
    /**
     * The signature of command.
     *
     * @var string
     */
    protected $signature = 'service:update {id}';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Change the status of service';

    /**
     * Execute the command.
     *
     * @see fire()
     * @return void
     */
    public function handle(){
        try
        {
            if(isset(config('appservices')[$this->argument('id')])) {
                $status = config('appservices')[$this->argument('id')];
                $choiceArr = [];
                if($status==0)
                    $choiceArr = ['ACTIVATE','CANCEL'];
                else if($status==1)
                    $choiceArr = ['DEACTIVATE','CANCEL'];
                $choice = $this->choice('The service is currently '.($status==0 ? 'INACTIVE':'ACTIVE').'. Would you like to '.($status==0 ? 'activate':'deactivate').' it?', $choiceArr, 0);
                if($choice=='ACTIVATE') {
                    //Add to cfg
                    $result = (new Config())->setServiceStatus($this->argument('id'), 1);
                    //Add to php unit
                    (new PhpunitXML())->addService($this->argument('id'));
                    $this->info('The service is now activated');
                } else if($choice=='DEACTIVATE') {
                    //Add to cfg
                    $result = (new Config())->setServiceStatus($this->argument('id'), 0);
                    //Remove from php unit
                    (new PhpunitXML())->removeService($this->argument('id'));
                    $this->info('The service is now deactivated');
                } else {
                    $this->info('Ok, the service state is unchanged');
                }

            } else {
                $this->error('Service '. $this->argument('id') . ' not found. Run php artisan service:list to see available services.');
                $this->comment('Note that services are case sensitive'); 
            }

        }
        catch(\Exception $e)
        {
            $this->error($e->getMessage());
        }
    }
}
