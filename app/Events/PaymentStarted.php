<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PaymentStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $paymentData;

    public function __construct($paymentData)
    {

        Log::info('constructor calisti!');
        
        $this->paymentData = $paymentData;
        
    }

    public function broadcastOn()
    {
        Log::info('broadcastOn çağrıldı!');
        return new PrivateChannel('payment.status');
        
    }
}
