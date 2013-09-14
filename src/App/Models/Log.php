<?php

namespace App\Models; 

use Framework\ActiveRecord;
use App\Models\Events\Event;

class Log extends ActiveRecord
{
    public $id;
    public $log;
    public $gameId;
    public $timeStart;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->timeStart = date('Y-m-d H:i:s', time());
    }
    
    public function getChangesSince($id)
    {
        $changes = array();
        
        if (!$this->log) {
            return $changes;
        }
        
        foreach ($this->log as $eventId => $event) {
            if ($eventId > $id) {
                $changes[] = $event->format();
            }
        }
        
        return $changes;
    }
    
    public function add(Event $event)
    {        
		$event->id = count($this->log);
		$event->timestamp = time();
        $this->log[$event->id] = $event;
        
        return $this;
    }
	
	public function getLastEventId()
	{
		return count($this->log) - 1;
	}
    
    public function afterFetch()
    {
        $this->log = unserialize($this->log);     
    }
    
    public function beforeSave()
    {
        if (!$this->log) {
            $this->log = array();            
        }
        $this->log = serialize($this->log);
    }
    
    public function afterSave()
    {
        $this->log = unserialize($this->log);
    }
    
    public function __toString() 
    {
        $logStr = sprintf('%s: Начало игры<br/>', $this->timeStart);
        
        foreach ($this->log as $event) {
            $date = date('Y-m-d H:i:s', $event->timestamp);
            $logStr .=  sprintf('%s: %s<br/>', $date, $event);
        }
        
        return $logStr;
    }
}