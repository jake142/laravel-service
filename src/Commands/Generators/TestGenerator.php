<?php namespace Jake142\LaravelPods\Commands\Generators;

use Jake142\LaravelPods\Commands\Generators\Generator;

/**
 * Class TestGenerator
 * @package Jake142\LaravelPods\Generators
 */
class TestGenerator
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
            'namespace' => 'namespace Pods\\'.$this->version.'\\'.$this->name.'\\Test',
            'service'   => strtolower($this->name),
            'version'   => strtolower($this->version),
        ];
        (new Generator(base_path().'/pods/'.$this->version.'/'.$this->name.'/Tests/ExampleTest.php', 'test', $replaces))->run();
    }
}
