<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\Subscriber;

class CreateSubscriber implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     * @param array $data
     */
    public function __construct(
        public array $data
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Subscriber::create($this->data);
    }
}
