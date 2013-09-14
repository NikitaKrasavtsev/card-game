<?php

namespace App\Models\Events\Formatters;

class TurnCardEventFormatter implements EventFormatter
{       
    public function format($event)
    {       
        return array(
			'eventId' => $event->id,
            'user' => array(
                'id' => $event->userId,
                'new' => false,
                'left' => false,
            ),
            'card' => $event->cardName, 
        );
    }    
}
