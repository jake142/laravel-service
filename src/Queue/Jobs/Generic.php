<?php namespace Jake142\Service\Queue\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * An Generic job
 */
class Generic implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $payload;

    public function fire($job)
    {
        $this->handle(json_decode($job->getRawBody(),true)['data']);
        $job->delete();
    }

    public function handle($payload)
    {
        //Magic goes here
    }
}