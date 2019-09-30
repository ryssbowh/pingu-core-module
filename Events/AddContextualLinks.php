<?php

namespace Pingu\Core\Events;

use Illuminate\Queue\SerializesModels;
use Pingu\Core\Contracts\Models\HasContextualLinksContract;

class AddContextualLinks
{
    use SerializesModels;

    public $object;
    public $links;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(HasContextualLinksContract $object, array &$links)
    {
        $this->object = $object;
        $this->links = $links;
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
