<?php namespace Jake142\LaravelPods\Commands\Generators;

use Jake142\LaravelPods\Commands\Generators\Generator;

/**
 * Class ComposerGenerator
 * @package Jake142\LaravelPods\Generators
 */
class ComposerGenerator
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
            'pod'     => $this->name,
            'version' => $this->version,
        ];
        (new Generator(base_path().'/pods/'.$this->version.'/'.$this->name.'/composer.json', 'composer', $replaces))->run();
    }
}
