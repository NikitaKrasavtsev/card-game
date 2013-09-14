<?php

namespace App\Controllers; 

use App\Models\Deck;
use App\Models\User;
use App\Models\Log;
use App\Models\Events\UserJoinEvent;
use App\Models\Events\UserLeaveEvent;
use App\Models\Events\TurnCardEvent;

class GamesController extends IndexController
{   
    private $_log;
    
    public function __construct()
    {
        parent::__construct();

        $user = $this->getUser();                 
        
        if (!$user) {
            $this->redirect();
        }
        
        $this->_log = $this->getLog();
    }
    
    public function indexAction()
    {
        $user = $this->getUser();
        
        $this->redirect('Games/show/' . $user->gameId);
    }
    
    public function showAction($id)
    {
        $user = $this->getUser();        

        if (!$user->cards) {
            $user->setCards(Deck::getCards());
            $user->save();
            
            $this->_log->add(new UserJoinEvent($user)); 
            $this->_log->save();
            
                                              
        }                        
        
        $users = User::findByGameId($user->gameId);        
        
        $this->render('pages/game', array(
            'users'=> $users,
            'currentUser' => $user,
			'log' => $this->_log,
        ));
    }
    
    public function turnAction()
    {
        $request = $this->get('request'); 
        
        if (!$request->isPost() || !$request->isAjax()) {
            $this->error(404);
        }               
        
        $user = $this->getUser();
        
        if (!$user) {
            $this->error(404);
        }
        
        $cardName = $request->getPostParam('card_name');
        
        try {
            $user->turnCard($cardName);
            $user->save();                        

            $this->_log->add(new TurnCardEvent($user, $cardName, $user->cards[$cardName]));
            $this->_log->save();
        } catch (\Exception $exc) {
            return $this->json(array('error' => $exc->getMessage()));
        }
        
        $this->json(array('success' => true));        
    }
    
    public function updateAction()
    {
        $request = $this->get('request');
        
        if (!$request->isPost() || !$request->isAjax()) {
            $this->error(404);
        }
        
        $lastEventId = $request->getPostParam('last_event_id');
                 
        $changes = $this->_log->getChangesSince($lastEventId);
        
        $this->json(array('changes' => $changes));
    }
    
    public function leaveAction()
    {        
        $user = $this->getUser();
        $gameId = $user->gameId;
        $user->delete();
        $this->logout();
        
        $users = User::findByGameId($gameId);
        if (!$users) {
            $this->_log->delete();
        }

        $this->_log->add(new UserLeaveEvent($user));
        $this->_log->save();
    }
    
    public function logAction()
    {
        $this->render('pages/log', array('log' => $this->_log), false);
    }
    
    private function getLog()
    {
        $user = $this->getUser();
        
        $log = Log::findOneByGameId($user->gameId);

        if (!$log) {
            $log = new Log;
            $log->gameId = $user->gameId;
            $log->save();
        }

        return $log;
    }
}
