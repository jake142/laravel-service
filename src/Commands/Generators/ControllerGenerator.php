<?php namespace Jake142\Service\Commands\Generators;

use Jake142\Service\Commands\Generators\Generator;

/**
 * Class ControllerGenerator
 * @package Jake142\Service\Generators
 */
class ControllerGenerator
{
    /**
     * The name of the service
     *
     * @var string
     */

    protected $name;

    /**
     * The version of the service
     *
     * @var string
     */

    protected $version;

    public function __construct(string $name, string $version)
    {
        $this->name = $name;
        $this->version = $version;
    }

    /**
     * Run the generator
     *
     */
    public function run()
    {
        $replaces = [
            'namespace' => 'namespace Services\\'.$this->version.'\\'.$this->name.'\\Http\\Controllers'
        ];
        (new Generator(base_path().'/Services/'.$this->version.'/'.$this->name.'/Http/Controllers/Service.php', 'controller', $replaces))->run();

        //Add the routes to the controller
        $replaces = [
            'namespace' => '\\Services\\'.$this->version.'\\'.$this->name.'\\Http\\Controllers',
            'service' => strtolower($this->name),
            'version' => strtolower($this->version)
        ];
        (new Generator(base_path().'/Services/'.$this->version.'/'.$this->name.'/routes/api.php', 'api_routes', $replaces))->run();
        $replaces = [
            'namespace' => '\\Services\\'.$this->version.'\\'.$this->name.'\\Http\\Controllers',
            'service' => strtolower($this->name),
            'version' => strtolower($this->version)
        ];
        (new Generator(base_path().'/Services/'.$this->version.'/'.$this->name.'/routes/web.php', 'web_routes', $replaces))->run();   
    }
}
