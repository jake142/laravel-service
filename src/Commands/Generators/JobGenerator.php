<?php namespace Jake142\Service\Commands\Generators;

use Jake142\Service\Commands\Generators\Generator;

/**
 * Class JobGenerator
 * @package Jake142\Service\Generators
 */
class JobGenerator
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
            'namespace' => 'namespace Services\\'.$this->version.'\\'.$this->name.'\\Jobs'
        ];
        (new Generator(base_path().'/Services/'.$this->version.'/'.$this->name.'/Jobs/ExampleJob.php', 'job', $replaces))->run();
    }
}
