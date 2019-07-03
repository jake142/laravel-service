<?php namespace Jake142\Service\Commands\Generators;

use Jake142\Service\Commands\Generators\Generator;

/**
 * Class ComposerGenerator
 * @package Jake142\Service\Generators
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
            'service_lower' => strtolower($this->name),
            'version_lower' => strtolower($this->version),
            'service' => $this->name,
            'version' => $this->version
        ];
        (new Generator(base_path().'/Services/'.$this->version.'/'.$this->name.'/composer.json', 'composer', $replaces))->run();
    }
}
