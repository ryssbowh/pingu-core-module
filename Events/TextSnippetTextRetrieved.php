<?php

namespace Pingu\Core\Events;

use Illuminate\Queue\SerializesModels;
use Pingu\Core\Entities\TextSnippet;

class TextSnippetTextRetrieved
{
    use SerializesModels;

    private $textSnippet;
    private $replacements;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(TextSnippet $textSnippet, array &$replacements)
    {
        $this->textSnippet = $textSnippet;
        $this->replacements = $replacements;
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
