<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LogoutAllUsersJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $response = Http::post(config('app.url') . '/api/admin/logout-all');

        Log::info('LogoutAllUsersJob HTTP Response:'. [
            'status' => $response->status(),
            'body' => $response->body()
        ]);
    }
}
