<?php namespace Jake142\Service\Queue\Jobs;

/**
 * An Generic job
 */
class Generic
{

    /**
     * @var array
     */
    protected $data;


    public function fire($data)
    {
        $this->data = json_decode($data->getRawBody(),true)['data'];
        $this->handle();
    }
}
