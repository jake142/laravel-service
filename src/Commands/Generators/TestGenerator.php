<?php namespace Jake142\Service\Commands\Generators;

use Jake142\Service\Commands\Generators\Generator;

/**
 * Class TestGenerator
 * @package Jake142\Service\Generators
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
            'namespace' => 'namespace Services\\'.$this->version.'\\'.$this->name.'\\Tests',
            'service' => strtolower($this->name),
            'version' => strtolower($this->version)
        ];
        (new Generator(base_path().'/Services/'.$this->version.'/'.$this->name.'/tests/ExampleTest.php', 'test', $replaces))->run();
    }
}
