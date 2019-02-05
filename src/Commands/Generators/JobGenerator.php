<?php namespace Jake142\LaravelPods\Commands\Generators;

use Jake142\LaravelPods\Commands\Generators\Generator;

/**
 * Class JobGenerator
 * @package Jake142\LaravelPods\Generators
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
            'namespace' => 'namespace Pods\\'.$this->version.'\\'.$this->name.'\\Jobs',
        ];
        (new Generator(base_path().'/pods/'.$this->version.'/'.$this->name.'/Jobs/ExampleJob.php', 'job', $replaces))->run();
    }
}
