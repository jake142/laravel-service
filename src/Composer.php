<?php namespace Jake142\LaravelPods;

use Illuminate\Support\Composer as BaseComposer;

class Composer extends BaseComposer
{
    /**
     * Add a pods
     *
     */
    public function addPod($version, $name)
    {

        $composer = $this->readComposer();
        if (isset($composer['repositories'])) {
            $composer['repositories'][] = ['type' => 'path', 'url' => 'pods/'.$version.'/'.$name, 'options' => ['symlink' => true]];
        } else {
            $composer['repositories'][] = ['type' => 'path', 'url' => 'pods/'.$version.'/'.$name, 'options' => ['symlink' => true]];
        }
        $composer['minimum-stability'] = 'dev';
        $composer['prefer-stable']     = true;
        $this->files->put(base_path().'/composer.json', json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Disable service
     *
     */
    public function disablePod($service)
    {
        $process = $this->getProcess();
        $process->setCommandLine(trim($this->findComposer().' remove pods/'.$service));
        $process->run();
    }

    /**
     * Enable service
     *
     */
    public function enablePod($service)
    {
        $process = $this->getProcess();
        $process->setCommandLine(trim($this->findComposer().' require pods/'.$service));
        $process->run();
    }

    /**
     * List pods
     *
     */
    public function listPods()
    {

        $composer = $this->readComposer();
        $services = [];
        if (isset($composer['repositories'])) {

            foreach ($composer['repositories'] as $repository) {
                if (isset($repository['url']) && strpos($repository['url'], 'pods/') === 0) {
                    $serviceName            = str_replace('pods/', '', $repository['url']);
                    $serviceEnabled         = (isset($composer['require']['pods/'.$serviceName]) ? 'ENABLED' : 'DISABLED');
                    $services[$serviceName] = $serviceEnabled;
                }
            }
        }
        return $services;
    }

    /**
     * Service is enabled
     *
     * @return boolean
     */
    public function podEnabled($service)
    {
        $composer = $this->readComposer();
        if (isset($composer['require']['pods/'.$service])) {
            return true;
        }

        return false;
    }

    /**
     * Service exist
     *
     * @return boolean
     */
    public function podExist($service)
    {
        $composer = $this->readComposer();
        if (isset($composer['repositories'])) {
            if (array_search('pods/'.$service, array_column($composer['repositories'], 'url')) !== false) {
                return true;
            }

        }
        return false;

    }

    /**
     * Read composer.json
     *
     * @return array
     */
    public function readComposer()
    {
        return json_decode(file_get_contents(base_path('composer.json')), true);
    }

    /**
     * Write composer.json
     *
     * @return array
     */
    public function writeComposer($config)
    {
        return file_put_contents(
            base_path('composer.json'),
            json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }
}
