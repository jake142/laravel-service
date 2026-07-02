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
    public function getPackageName($version, $name)
    {
        return 'laravel-service/'.strtolower($version.'-'.$name);
    }

    /**
     * Add a path repository for a service (idempotent).
     */
    public function addService($version, $name)
    {
        $packageName = $this->getPackageName($version, $name);
        $path = 'Services/'.$version.'/'.$name;
        $composerData = $this->readComposer();

        if (isset($composerData['repositories'])) {
            foreach ($composerData['repositories'] as $repository) {
                if ((isset($repository['name']) && $repository['name'] === $packageName)
                    || (isset($repository['url']) && $repository['url'] === $path)) {
                    return;
                }
            }
        } else {
            $composerData['repositories'] = [];
        }

        $composerData['repositories'][] = [
            'name' => $packageName,
            'type' => 'path',
            'url' => $path,
            'options' => ['symlink' => true],
        ];
        $composerData['minimum-stability'] = 'dev';
        $composerData['prefer-stable'] = true;
        $this->writeToDisk($composerData);
    }

    /**
     * Find a service on disk by package name (laravel-service/v1-foo).
     *
     * @return array{version: string, name: string}|null
     */
    public function discoverServiceOnDisk($packageName)
    {
        if (!preg_match('#^laravel-service/(.+)$#', strtolower($packageName), $matches)) {
            return null;
        }

        $slug = $matches[1];
        $servicesPath = base_path('Services');

        if (!is_dir($servicesPath)) {
            return null;
        }

        foreach (new \DirectoryIterator($servicesPath) as $versionDir) {
            if (!$versionDir->isDir() || $versionDir->isDot()) {
                continue;
            }

            $version = $versionDir->getFilename();

            foreach (new \DirectoryIterator($versionDir->getPathname()) as $serviceDir) {
                if (!$serviceDir->isDir() || $serviceDir->isDot()) {
                    continue;
                }

                $name = $serviceDir->getFilename();

                if (strtolower($version.'-'.$name) === $slug) {
                    return ['version' => $version, 'name' => $name];
                }
            }
        }

        return null;
    }

    /**
     * Register path repo from composer.json or from Services/ on disk.
     */
    public function registerServiceFromPackageName($packageName)
    {
        $packageName = strtolower($packageName);

        if ($this->serviceExist($packageName)) {
            return true;
        }

        $discovered = $this->discoverServiceOnDisk($packageName);

        if ($discovered === null) {
            return false;
        }

        $this->addService($discovered['version'], $discovered['name']);

        return true;
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
        $service = strtolower($service);
        $this->normalizeLaravelServiceConstraints();

        $command = array_merge(
            (is_array($this->findComposer()) ? $this->findComposer() : [$this->findComposer()]),
            ['require', $service.':@dev', '--ignore-platform-reqs']
        );
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
        $service = strtolower($service);
        $this->normalizeLaravelServiceConstraints();

        $command = array_merge(
            (is_array($this->findComposer()) ? $this->findComposer() : [$this->findComposer()]),
            ['remove', $service, '--ignore-platform-reqs']
        );
        $process = $this->createProcess($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    /**
     * Path-repo versions follow the current git branch (dev-main, dev-sandbox, …).
     * Normalize all laravel-service/* constraints to @dev so branch switches work.
     */
    public function normalizeLaravelServiceConstraints()
    {
        $composerData = $this->readComposer();
        $changed = false;

        if (!isset($composerData['require'])) {
            return;
        }

        foreach (array_keys($composerData['require']) as $package) {
            if (strpos($package, 'laravel-service/') !== 0) {
                continue;
            }

            if (($composerData['require'][$package] ?? null) === '@dev') {
                continue;
            }

            $composerData['require'][$package] = '@dev';
            $changed = true;
        }

        if ($changed) {
            $this->writeToDisk($composerData);
        }
    }

    /**
     * Re-resolve path-repo packages after branch switches (updates composer.lock).
     */
    public function syncLaravelServices()
    {
        $this->normalizeLaravelServiceConstraints();

        $command = array_merge(
            (is_array($this->findComposer()) ? $this->findComposer() : [$this->findComposer()]),
            ['update', 'laravel-service/*', '--ignore-platform-reqs']
        );
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
        $this->files->put(base_path().'/composer.json', json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
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