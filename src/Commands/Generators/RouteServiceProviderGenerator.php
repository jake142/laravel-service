<?php namespace Jake142\Service\Commands\Generators;

use Jake142\Service\Commands\Generators\Generator;

/**
 * Class RouteServiceProviderGenerator
 * @package Jake142\Service\Generators
 */
class RouteServiceProviderGenerator
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
            'namespace' => 'namespace Services\\'.$this->version.'\\'.$this->name.'\\Providers',
            'service' => $this->name,
            'version' => $this->version
        ];
        (new Generator(base_path().'/Services/'.$this->version.'/'.$this->name.'/Providers/RouteServiceProvider.php', 'route_service_provider', $replaces))->run(); 
    }
}
