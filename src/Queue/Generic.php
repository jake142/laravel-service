<?php namespace Jake142\Service\Queue;

use Queue;

class Generic
{

    /**
     * @var string
     */
    protected $job;
    /**
     * @var array
     */
    protected $data;
    /**
     * @var string
     */
    protected $queue;
    /**
     * @var array
     */
    protected $options;
    /**
     * @param string $job
     * @param array  $data
     * @param string $queue
     */
    public function __construct(string $job, array $data, string $queue = null)
    {
        $this->job = $job;
        $this->data = $data;
        $this->queue = $queue;
    }
    public function dispatch()
    {
        Queue::push($this->job, $this->data, $this->queue);
    }
}