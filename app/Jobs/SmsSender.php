<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use const http\Client\Curl\Features\HTTP2;

class SmsSender implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected User $user;
    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Http::post('https://rest.nexmo.com/sms/json', [
            'from'          => 'Chainsaw Man',
            'api_key'       => '78addf73',
            'api_secret'    => 'lRbiZj7upNOyROTx',
            'to' => '52'.$this->user->phone,
            'text' => 'Your code is: '.$this->user->code
        ]);
    }
}


