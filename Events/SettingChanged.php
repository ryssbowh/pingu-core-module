<?php

namespace Pingu\Core\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SettingChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $name,$value,$details;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( $name, $value, $details )
    {
        $this->name = $name;
        $this->value = $value;
        $this->details = $details;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
