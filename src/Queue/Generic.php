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
     * @param array $options
     */
    public function __construct(string $job, array $data, string $queue = null, array $options = [])
    {
        $this->job = $job;
        $this->data = $data;
        $this->queue = $queue;
        $this->options = $options;
    }
    public function dispatch()
    {
        $payload = [];
        $payload['job'] = $this->job;
        $payload['data'] = $this->data;
        Queue::pushRaw(json_encode($payload), $this->queue, $this->options);
    }
}