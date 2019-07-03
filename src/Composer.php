<?php namespace Jake142\Service;

use Illuminate\Support\Composer as BaseComposer;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
        $composerData = $this->readComposer();
        $composerData['repositories'][] = ['name'=>'laravel-service/'.strtolower($version.'-'.$name), 'type'=>'path','url'=>'Services/'.$version.'/'.$name,'options'=>['symlink'=>true]];
        $composerData['minimum-stability'] = 'dev';
        $composerData['prefer-stable'] = true;
        $this->writeToDisk($composerData);
    }
    /**
     * List services
     *
     */
    public function listServices()
    {
        $composerData = $this->readComposer();
        $services = [];
        if(isset($composerData['repositories'])) {

            foreach($composerData['repositories'] as $repository)
            {
                if(isset($repository['name']) && strpos($repository['url'], 'Services/') === 0) {
                     $serviceEnabled = $this->serviceEnabled($repository['name']) ? 'ENABLED':'DISABLED';
                     $services[$repository['name']] = $serviceEnabled;
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
        $process->setCommandLine(trim($this->findComposer().' require '.$service));
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
    /**
     * Disable service
     *
     */
    public function disableService($service)
    {
        $process = $this->getProcess();
        $process->setCommandLine(trim($this->findComposer().' remove '.$service));
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
    /**
     * Service exist
     *
     * @return boolean
     */
    public function serviceExist($service)
    {
        $composerData = $this->readComposer();
        if(isset($composerData['repositories'])) {
            if(array_search($service, array_column($composerData['repositories'], 'name')) !== False)
                return true;
        }
        return false;
        
    }
    /**
     * Status of service
     *
     * @return boolean
     */
    public function serviceEnabled($service)
    {
        $composerData = $this->readComposer();
        if(isset($composerData['require'][$service]))
            return true;

        return false;
    }
    /**
     * Write composer file
     *
     * @return boolean
     */
    public function writeToDisk(array $composerData)
    {
        $this->files->put(base_path().'/composer.json', json_encode($composerData,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_SLASHES));
    }
}