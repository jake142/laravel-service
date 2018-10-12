<?php namespace Jake142\Service;

use Illuminate\Support\Composer as BaseComposer;

class Composer extends BaseComposer
{
    /**
     * Read the composer.json
     *
     * @return array
     */
    public function readComposer()
    {
        return json_decode(file_get_contents(base_path() . '/composer.json'), true);
    }
    /**
     * Add a service
     *
     */
    public function addService($version, $name)
    {

        $composer = $this->readComposer();
        if(isset($composer['repositories'])) {
            $composer['repositories'][] = ['type'=>'path','url'=>'Services/'.$version.'/'.$name,'options'=>['symlink'=>true]];
        } else {
            $composer['repositories'][] = ['type'=>'path','url'=>'Services/'.$version.'/'.$name,'options'=>['symlink'=>true]];
        }
        $composer['minimum-stability'] = 'dev';
        $composer['prefer-stable'] = true;
        $this->files->put(base_path().'/composer.json', json_encode($composer,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_SLASHES));
    }
    /**
     * List services
     *
     */
    public function listServices()
    {

        $composer = $this->readComposer();
        $services = [];
        if(isset($composer['repositories'])) {

            foreach($composer['repositories'] as $repository)
            {
                if(isset($repository['url']) && strpos($repository['url'], 'Services/') === 0) {
                     $serviceName = str_replace('Services/','',$repository['url']);
                     $serviceEnabled = (isset($composer['require'][$serviceName]) ? 'ENABLED':'DISABLED');
                     $services[$serviceName] = $serviceEnabled;
                }
            }
        }
        return $services;
    }
    /**
     * Enable service
     *
     */
    public function enableService($service)
    {
        $process = $this->getProcess();
        $process->setCommandLine(trim($this->findComposer().' require Services/'.$service));
        $process->run();
    }
    /**
     * Disable service
     *
     */
    public function disableService($service)
    {
        $process = $this->getProcess();
        $process->setCommandLine(trim($this->findComposer().' remove Services/'.$service));
        $process->run();
    }
    /**
     * Service exist
     *
     * @return boolean
     */
    public function serviceExist($service)
    {
        $composer = $this->readComposer();
        if(isset($composer['repositories'])) {
            if(array_search('Services/'.$service, array_column($composer['repositories'], 'url')) !== False)
                return true;
        }
        return false;
        
    }
    /**
     * Service is enabled
     *
     * @return boolean
     */
    public function serviceEnabled($service)
    {
        $composer = $this->readComposer();
        if(isset($composer['require']['Services/'.$service]))
            return true;

        return false;
    }
}