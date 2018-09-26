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
        return config('laravel-service');
    }
    /**
     * Add a service
     *
     */
    public function addService($version, $name)
    {

        $config = $this->readConfig();
        $config[$version.'/'.$name] = ['status'=>0];
        $this->filesystem->put(base_path().'/config/laravel-service.php', $this->searilizeConfig($config));
    }
    /**
     * Set a service
     *
     */
    public function setServiceStatus($id, $status)
    {

        $config = $this->readConfig();
        $config[$id]['status'] = $status;
        $this->filesystem->put(base_path().'/config/laravel-service.php', $this->searilizeConfig($config));
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
            $arrayStr = $arrayStr."\t'".$key."' => [".PHP_EOL."\t\t'status' => ".$value['status'].",".PHP_EOL
            ."\t\t"."'cfg' => call_user_func(function() {".PHP_EOL
            ."\t\t\t\$configs = [];".PHP_EOL
            ."\t\t\tforeach (glob(app_path().'/Services/".$key."/config/*.php') as \$appCfg)".PHP_EOL
            ."\t\t\t\t\$configs[basename(\$appCfg,'.php')] = include \$appCfg;".PHP_EOL
            ."\t\t\treturn \$configs;".PHP_EOL
            ."\t\t})".PHP_EOL."\t],".PHP_EOL;

        return $arrayStr.'];';
    }
}