<?php namespace Jake142\LaravelPods\Commands;

use Illuminate\Console\Command;
use Jake142\LaravelPods\Composer;
use Jake142\LaravelPods\PhpunitXML;

class DisablePods extends Command
{
    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Disable a pod';

    /**
     * The name of command.
     *
     * @var string
     */
    protected $signature = 'pods:disable {pod}';

    /**
     * Execute the command.
     *
     * @see fire()
     * @return void
     */
    public function handle(Composer $composer, PhpunitXML $phpUnitXML)
    {
        try
        {
            $pod = $this->argument('pod');
            if (!$composer->podExist($pod)) {
                throw new \Exception('Pod do not exist');
            }

            if (!$composer->podEnabled($pod)) {
                throw new \Exception('Pod is already disabled');
            }

            $this->info('Disabling '.$pod.'...');
            $composer->disablePod($pod);
            $phpUnitXML->disablePod($pod);
            $this->info($pid.' is now disabled');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
