<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\Notifications as EventNotifications;
use Pusher\Pusher;
class Notifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $type;
    public $data;
    public $pusher;
    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
        $options = array(
            'cluster' => 'ap1',
            'useTLS' => true
        );
        $this->pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ($this->type) {
            case 'notifications':
                $this->pusher->trigger('payhiram', 'Notifications', $this->data);
                break;
            case 'message':
                $this->pusher->trigger('payhiram', 'Message', $this->data);
            case 'validation':
                $this->pusher->trigger('payhiram', 'Validation', $this->data);
            default:
                # code...
                break;
        }
    }
}
