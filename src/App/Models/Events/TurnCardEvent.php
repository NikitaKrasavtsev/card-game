<?php

namespace App\Models\Events; 

use App\Models\Events\Formatters\TurnCardEventFormatter;
use App\Models\User;

class TurnCardEvent extends Event
{
    public $cardName; 
    
    public $cardOpened;    
    
    public static $cardsValues = array(
        '2' => 'Двойка',
        '3' => 'Тройка', 
        '4' => 'Четверка', 
        '5' => 'Пятерка', 
        '6' => 'Шестерка', 
        '7' => 'Семерка', 
        '8' => 'Восьмерка', 
        '9' => 'Девятка', 
        '10' => 'Десятка', 
        'J' => 'Валет', 
        'Q' => 'Дама', 
        'K' => 'Король', 
        'A' => 'Туз'        
    );
    
    public static $cardsSuits = array(
        'S' => 'пик',
        'C' => 'крестей',
        'D' => 'бубей',
        'H' => 'червей'
    );
    
    public function __construct(User $user, $cardName, $cardOpened)    
    {
        parent::__construct($user);
        
        $this->cardName = $cardName; 
        $this->cardOpened = $cardOpened;
    }
    
    public function getFormatter() 
    {
        return new TurnCardEventFormatter;
    }
    
    public function __toString()
    {
        $action = $this->cardOpened ? ' показал' : ' скрыл';
        $cardName = str_split($this->cardName);
        
        $cardSuit = $cardName[0];
        if (count($cardName) > 2) {
           $cardValue = $cardName[1] . $cardName[2];
        } else {
            $cardValue = $cardName[1];
        }

        return sprintf('Пользователь %s (id: %s) %s карту «%s %s».', $this->userName, $this->userId, $action, self::$cardsValues[$cardValue], self::$cardsSuits[$cardSuit]);
    }
}