<?php

namespace App\Models\Events;

use App\Models\Events\Formatters\UserJoinEventFormatter;
use App\Models\User;

class UserJoinEvent extends Event
{
    public $userCards;
    
    public function __construct(User $user)
    {
        parent::__construct($user);

        $this->userCards = $user->cards;
    }
    
    public function getFormatter()
    {
        return new UserJoinEventFormatter;
    }
    
    public function __toString()
    {
        return sprintf('Пользователь %s (id: %s) присоединился к игре.', $this->userName, $this->userId);
    }
}