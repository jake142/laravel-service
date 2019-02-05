<?php namespace Jake142\LaravelPods\Commands\Generators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class Generator
{

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * The path to write the generated file
     *
     * @var array
     */
    protected $path;

    /**
     * The shortname of stub.
     *
     * @var string
     */
    protected $stub;

    /**
     * Array of replaces values
     *
     * @var array
     */
    protected $replaces;

    /**
     * Create new instance of this class.
     *
     * @param array $options
     */
    public function __construct(string $path, string $stub, array $replaces)
    {
        $this->filesystem = new Filesystem;
        $this->path = $path;
        $this->stub = $stub;
        $this->replaces = $replaces;
    }
    /**
     * Get stub template for generated file.
     *
     * @return string
     */
    public function getStub()
    {
        $contents = file_get_contents(__DIR__.'/Stubs/' . $this->stub . '.stub');
        foreach ($this->replaces as $search => $replace) {
            $contents = str_replace('$' . strtoupper($search) . '$', $replace, $contents);
        }
        return $contents;
    }
    /**
     * Run the generator.
     *
     * @return int
     * @throws FileAlreadyExistsException
     */
    public function run()
    {

        return $this->filesystem->put($this->path, $this->getStub());
    }
}
