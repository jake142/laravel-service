<?php namespace Jake142\LaravelPods\Commands\Generators;

use Illuminate\Filesystem\Filesystem;

/**
 * Class PodGenerator
 * @package Jake142\LaravelPods\Generators
 */
class PodGenerator
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * The name of the pod
     *
     * @var string
     */

    protected $name;

    /**
     * The version of the pod
     *
     * @var string
     */

    protected $version;

    public function __construct(string $name, string $version)
    {
        $this->name       = $name;
        $this->version    = $version;
        $this->filesystem = new Filesystem;
    }

    /**
     * Check if pod exist
     *
     */
    public function exist()
    {
        if (file_exists(base_path().'/pods/'.$this->version.'/'.$this->name)) {
            throw new \Exception('Pod already exist');
        } else {
            return $this;
        }
    }

    /**
     * Run the Job
     *
     */
    public function run()
    {
        $this->filesystem->makeDirectory(base_path().'/pods/'.$this->version.'/'.$this->name.'/Providers', 0777, true, true);
        $this->filesystem->makeDirectory(base_path().'/pods/'.$this->version.'/'.$this->name.'/Http/Controllers', 0777, true, true);
        $this->filesystem->makeDirectory(base_path().'/pods/'.$this->version.'/'.$this->name.'/Http/Middleware', 0777, true, true);
        $this->filesystem->makeDirectory(base_path().'/pods/'.$this->version.'/'.$this->name.'/Models', 0777, true, true);
        $this->filesystem->makeDirectory(base_path().'/pods/'.$this->version.'/'.$this->name.'/Jobs', 0777, true, true);
        $this->filesystem->makeDirectory(base_path().'/pods/'.$this->version.'/'.$this->name.'/Routes', 0777, true, true);
        $this->filesystem->makeDirectory(base_path().'/pods/'.$this->version.'/'.$this->name.'/Tests', 0777, true, true);
        // $this->filesystem->makeDirectory(base_path().'/pods/'.$this->version.'/'.$this->name.'/config', 0777, true, true);
        // $this->filesystem->makeDirectory(base_path().'/pods/'.$this->version.'/'.$this->name.'/migrations', 0777, true, true);
        $this->filesystem->makeDirectory(base_path().'/pods/'.$this->version.'/'.$this->name.'/Views', 0777, true, true);
    }
}
