<?php

namespace App\Models\Events; 

use App\Models\User;

abstract class Event
{	
    public $userId; 
    
    public $userName;
	
	public $timestamp;
    
    public function __construct(User $user)
    {
        $this->userId = $user->id;
        $this->userName = $user->name;
    }
    
    public function format()
    {
        return $this->getFormatter()->format($this);
    }        
    
    public function __toString()
    {
        
    }
    
    abstract public function getFormatter();
}