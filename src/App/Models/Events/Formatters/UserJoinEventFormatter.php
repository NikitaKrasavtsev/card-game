<?php

namespace App\Models\Events\Formatters;

use App\Models\User;
use Framework\Renderer;

class UserJoinEventFormatter implements EventFormatter
{
    private $_renderer;
    
    public function __construct()
    {
        $this->_renderer = new Renderer();
    }
    
    public function format($event)
    {
        $user = new User; 
        $user->id = $event->userId;
        $user->name = $event->userName; 
        $user->cards = $event->userCards;
        
        $output = array(
			'eventId' => $event->id,
            'user' => array(
                'id' => $event->userId,
                'new' => true,
                'left' => false,
                'cards' => $user->cards,
            ),
            'html'   => $this->_renderer->render('widgets/user', array('user' => $user, 'currentUser' => false)),  
        );
        
        return $output;
    }
}