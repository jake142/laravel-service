<?php namespace Jake142\LaravelPods\Commands;

use Illuminate\Console\Command;
use Jake142\LaravelPods\Composer;

class ListPods extends Command
{
    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'List all pods';

    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'pods:list';

    /**
     * Execute the command.
     *
     * @see fire()
     * @return void
     */
    public function handle(Composer $composer)
    {
        try
        {
            $pods = $composer->listPods();
            if (empty($pods)) {
                $this->error('You have no pods created. Run php artisan pods:make');
            } else {
                foreach ($pods as $key => $value) {
                    if ('ENABLED' == $value) {
                        $this->info($key.' status:'.$value);
                    } else {
                        $this->error($key.' status:'.$value);
                    }
                }
            }

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
