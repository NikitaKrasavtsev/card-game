<?php

namespace App\Models\Events\Formatters;

class UserLeaveEventFormatter implements EventFormatter
{    
    public function format($event)
    {        
        return array(
			'eventId' => $event->id,		
            'user' => array(
                'id' => $event->userId,
                'new' => false,
                'left' => true,
            ),
        );
    }
}