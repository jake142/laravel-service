<?php namespace Jake142\Service\Commands\Generators;

use Illuminate\Filesystem\Filesystem;

/**
 * Class ServiceGenerator
 * @package Jake142\Service\Generators
 */
class ServiceGenerator
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

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
        $this->filesystem = new Filesystem;
    }

    /**
     * Run the Job
     *
     */
    public function run()
    {
        $this->filesystem->makeDirectory(base_path().'/Services/'.$this->version.'/'.$this->name.'/Providers', 0777, true, true);
        $this->filesystem->makeDirectory(base_path().'/Services/'.$this->version.'/'.$this->name.'/Http/Controllers', 0777, true, true);
        $this->filesystem->makeDirectory(base_path().'/Services/'.$this->version.'/'.$this->name.'/Http/Middleware', 0777, true, true);
        $this->filesystem->makeDirectory(base_path().'/Services/'.$this->version.'/'.$this->name.'/Models', 0777, true, true);
        $this->filesystem->makeDirectory(base_path().'/Services/'.$this->version.'/'.$this->name.'/Jobs', 0777, true, true);
        $this->filesystem->makeDirectory(base_path().'/Services/'.$this->version.'/'.$this->name.'/routes', 0777, true, true);
        $this->filesystem->makeDirectory(base_path().'/Services/'.$this->version.'/'.$this->name.'/tests', 0777, true, true);
        $this->filesystem->makeDirectory(base_path().'/Services/'.$this->version.'/'.$this->name.'/config', 0777, true, true);
        $this->filesystem->makeDirectory(base_path().'/Services/'.$this->version.'/'.$this->name.'/migrations', 0777, true, true);
    }
    /**
     * Check if service exist
     *
     */
    public function exist()
    {
        if(file_exists(base_path().'/Services/'.$this->version.'/'.$this->name)) {
            throw new \Exception('Service already exist');
        } else {
            return $this;
        }
    }
}
