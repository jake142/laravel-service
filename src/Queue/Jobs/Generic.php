<?php namespace Jake142\LaravelPods\Queue\Jobs;

/**
 * An Generic job
 */
trait Generic
{

    /**
     * @var array
     */
    protected $data;

    public function handle()
    {
        //The magic happens here
    }
    public function fire($data)
    {
        $this->data = json_decode($data->getRawBody(),true)['data'];
        $this->handle();
    }
}
