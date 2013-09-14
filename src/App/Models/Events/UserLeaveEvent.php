<?php

namespace App\Models\Events; 

use App\Models\Events\Formatters\UserLeaveEventFormatter;

class UserLeaveEvent extends Event
{   
    public function getFormatter() 
    {
        return new UserLeaveEventFormatter;
    }
    
    public function __toString()
    {
        return sprintf('Пользователь %s (id: %s) покинул игру', $this->userName, $this->userId);
    }
}