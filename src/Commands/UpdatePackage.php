<?php namespace Jake142\Service\Commands;

use Illuminate\Console\Command;
use Jake142\Service\Composer;

class UpdatePackage extends Command
{
    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'laravel-service:update-package';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Makes changes to the package for new releases';

    /**
     * Execute the command.
     *
     * @see fire()
     * @return void
     */
    public function handle(Composer $composer){
        try
        {
            //Create optional stuff
            $createController = $this->choice('The upgrade to version 0.2.2 will disable all services. Should we proceed?', ['Yes','No'], 0);

            if($createController=='Yes') {
                $this->version022($composer);
            } else {
                $this->info('Ok, no upgrade done');
            }
            
        }
        catch(\Exception $e)
        {
            $this->error($e->getMessage());
        }
 
    }
    private function version022(Composer $composer) {
        try {

            $this->info('Begins upgrade to version 0.2.2');
            $composerFile = $composer->readComposer();
            $services = [];
            if(isset($composerFile['repositories'])) {
                foreach($composerFile['repositories'] as $key => $repository)
                {
                    if(!isset($repository['name']) && strpos($repository['url'], 'Services/') === 0) {
                        
                        //Get some data
                        $urlParts = explode('/',$repository['url']);
                        $serviceName = 'laravel-service/'.strtolower($urlParts[1].'-'.$urlParts[2]);
                        //Update main composer file
                        $composerFile['repositories'][$key]['name'] = $serviceName;
                        //Disable the service (needs to be manually enabled)
                        if(isset($composerFile['require'][$repository['url']])) {
                            $this->info('Disabling '.$repository['url']. '. You need to enable it using '.$serviceName); 
                            $composer->disableService($repository['url']);
                        }

                        $serviceComposerFile = json_decode(file_get_contents(base_path($repository['url'].'/composer.json')), true);
                        $serviceComposerFile['name'] = $serviceName;
                        file_put_contents(base_path($repository['url'].'/composer.json'), json_encode($serviceComposerFile,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_SLASHES));
                        $this->info('Has updated '.$repository['url']. ' to name '.$serviceName);
                    }
                }
                $composer->writeToDisk($composerFile);
                $this->info('Has written new composer.json files with the new names');
            }
            $this->info('Upgrade to version 0.2.2 complete. You can now enable your services again!');
        }
        catch(\Exception $e)
        {
            throw $e;
        }
    }
}
