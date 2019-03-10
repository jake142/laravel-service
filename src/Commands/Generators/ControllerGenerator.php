<?php namespace Jake142\LaravelPods\Commands\Generators;

use Jake142\LaravelPods\Commands\Generators\Generator;

/**
 * Class ControllerGenerator
 * @package Jake142\LaravelPods\Generators
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
        $this->name    = $name;
        $this->version = $version;
    }

    /**
     * Run the generator
     *
     */
    public function run()
    {
        $replaces = [
            'namespace' => 'namespace Pods\\'.$this->version.'\\'.$this->name.'\\Http\\Controllers',
        ];
        (new Generator(base_path().'/pods/'.$this->version.'/'.$this->name.'/Http/Controllers/Service.php', 'controller', $replaces))->run();

        //Add the routes to the controller
        $replaces = [
            'namespace' => '\\Pods\\'.$this->version.'\\'.$this->name.'\\Http\\Controllers',
            'pod'       => strtolower($this->name),
            'version'   => strtolower($this->version),
        ];
        (new Generator(base_path().'/pods/'.$this->version.'/'.$this->name.'/Routes/api.php', 'api_routes', $replaces))->run();
        $replaces = [
            'namespace' => '\\Pods\\'.$this->version.'\\'.$this->name.'\\Http\\Controllers',
            'pod'       => strtolower($this->name),
            'version'   => strtolower($this->version),
        ];
        (new Generator(base_path().'/pods/'.$this->version.'/'.$this->name.'/Routes/web.php', 'web_routes', $replaces))->run();
    }
}
