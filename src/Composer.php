<?php namespace Jake142\Service;

use Illuminate\Support\Composer as BaseComposer;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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
        //$command = array_merge((is_array($this->findComposer()) ? $this->findComposer():[$this->findComposer()]), ['require'], [$service, 'dev-master']);
        $command = array_merge((is_array($this->findComposer()) ? $this->findComposer():[$this->findComposer()]), ['require'], [$service]);
        $process = $this->createProcess($command);
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
        $command = array_merge((is_array($this->findComposer()) ? $this->findComposer():[$this->findComposer()]), ['remove'], [$service]);
        $process = $this->createProcess($command);
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
     * Get URL of a service
     *
     * @return string
     */
    public function getUrl($service)
    {
        $composerData = $this->readComposer();
        foreach($composerData['repositories'] as $key => $repository)
        {
            if(isset($repository['name']) && isset($repository['url']) && $repository['name'] == $service)
                return $repository['url'];
        }
        return null;
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
    /**
     * Override the deprecated method of get Process
     * Get a new Symfony process instance.
     *
     * @param  array  $command
     * @return \Symfony\Component\Process\Process
     */
    protected function createProcess(array $command)
    {
        return (new Process($command, $this->workingPath))->setTimeout(null);
    }
}