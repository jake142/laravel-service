<?php namespace Jake142\Service;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class Config
{

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Create new instance of this class.
     *
     */
    public function __construct()
    {
        $this->filesystem = new Filesystem;
    }
    /**
     * Read the config
     *
     * @return array
     */
    public function readConfig()
    {
        return config('appservices');
    }
    /**
     * Add a service
     *
     */
    public function addService($version, $name)
    {

        $config = $this->readConfig();
        $config[$version.'/'.$name] = 0;
        $this->filesystem->put(base_path().'/config/appservices.php', $this->searilizeConfig($config));
    }
    /**
     * Set a service
     *
     */
    public function setServiceStatus($id, $status)
    {

        $config = $this->readConfig();
        $config[$id] = $status;
        $this->filesystem->put(base_path().'/config/appservices.php', $this->searilizeConfig($config));
    }
    private function searilizeConfig(array $config)
    {
        $arrayStr = '<?php
    return [

        /*
        |--------------------------------------------------------------------------
        | All app services are listed here
        |--------------------------------------------------------------------------
        |
        | This file is populated automatically by the service commands
        |
        */'.PHP_EOL;

        foreach($config as $key => $value)
            $arrayStr = $arrayStr."\t\t'".$key."' => ".$value.",".PHP_EOL;

        return $arrayStr.'];';
    }
}