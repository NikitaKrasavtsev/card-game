<?php

namespace App\Models; 

class Deck
{    
    const CARDS_PER_PLAYER = 10;
    
    public static $cards = array(
        'S2', 'S3', 'S4', 'S5', 'S6', 'S7', 'S8', 'S9', 'S10', 'SJ', 'SQ', 'SK', 'SA',
        'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'C9', 'C10', 'CJ', 'CQ', 'CK', 'CA',
        'H2', 'H3', 'H4', 'H5', 'H6', 'H7', 'H8', 'H9', 'H10', 'HJ', 'HQ', 'HK', 'HA',
        'D2', 'D3', 'D4', 'D5', 'D6', 'D7', 'D8', 'D9', 'D10', 'DJ', 'DQ', 'DK', 'DA',
    );
    
    public static function getCards()
    {
        shuffle(self::$cards);
        $cards = array();
        
        for ($i = 0; $i < self::CARDS_PER_PLAYER; $i++) {
            $cards[$i] = self::$cards[$i];
        }
        
        return $cards;
    }
}