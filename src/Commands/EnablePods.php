<?php namespace Jake142\LaravelPods\Commands;

use Illuminate\Console\Command;
use Jake142\LaravelPods\Composer;
use Jake142\LaravelPods\PhpunitXML;

class EnablePods extends Command
{
    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Enable a pod';

    /**
     * The name of command.
     *
     * @var string
     */
    protected $signature = 'pods:enable {pod}';

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

            if ($composer->podEnabled($pod)) {
                throw new \Exception('Pod is already enabled');
            }

            $this->info('Enabling '.$pod.'...');
            $composer->enablePod($pod);
            $phpUnitXML->enablePod($pod);
            $this->info($pod.' is now enabled and ready to be used');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
