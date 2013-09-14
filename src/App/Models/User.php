<?php

namespace App\Models; 

use Framework\ActiveRecord;

class User extends ActiveRecord
{   
    public $id;
    public $name; 
    public $cards;
    public $gameId; 
    public $ip;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->setIp();
    }
    
    public function setCards($cards) 
    {
        $userCards = array();
        $opened = false;
        
        foreach ($cards as $card) {
            $this->cards[$card] = $opened;
        }

        return $this;
    }    
    
    public function turnCard($cardName)
    {
        if (!isset($this->cards[$cardName])) {
            throw new \Exception('Пользователь не обладает заданной картой');
        }
        
        $this->cards[$cardName] = !$this->cards[$cardName];                
        
        return $this;
    }        
    
    public function isValid()
    {
        if (!$this->name) {
            $this->_errors[] = 'Введите имя пользователя.';
        }
        
        if (!is_numeric($this->gameId)) {
            $this->_errors[] = 'Ключ игры должен содержать только цифры';
        }
        
        if ($this->isNew()) {
            $user = User::findByNameAndGameId($this->name, $this->gameId);
            if ($user) {
                $this->_errors[] = 'Пользователь с таким именем уже существует.';                        
            }
        }
        
        if ($this->_errors) {
            return false;
        }
        
        return true;
    }

    public function getErrors()
    {
        return $this->_errors;
    }
    
    public function afterFetch() 
    {
        $this->cards = json_decode($this->cards, true);
    }
    
    public function beforeSave()
    {
        if ($this->cards && is_array($this->cards)) {
            $this->cards = json_encode($this->cards);
        }
    }
    
    public function afterSave()
    {
        $this->cards = json_decode($this->cards, true);
    }
        
    private function setIp() 
    {
        if ($this->ip) {
            return $this;
        }
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {     
            $this->ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $this->ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return $this;
    }    
}