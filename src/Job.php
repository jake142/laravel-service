<?php namespace Jake142\Service;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

/**
 * An example job. Note the queue attribute beeing set to a unique service
 */
abstract class Job implements ShouldQueue
{
	use InteractsWithQueue, Queueable, SerializesModels;

    protected $queueName;
    protected $priority;

    public function __construct()
    {
        self::onQueue($this->queueName.'*'.$this->priority);
    }
}
