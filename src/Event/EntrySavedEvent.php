<?php

namespace Universal Reading\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Universal Reading\Entity\Entry;

/**
 * This event is fired as soon as an entry was saved.
 */
class EntrySavedEvent extends Event
{
    public const NAME = 'entry.saved';

    public function __construct(
        protected Entry $entry,
    ) {
    }

    public function getEntry(): Entry
    {
        return $this->entry;
    }
}
