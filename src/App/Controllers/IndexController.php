<?php

namespace App\Controllers; 

use Framework\Controller;
use App\Models\Deck;
use App\Models\User;
use App\Models\Log;
use App\Models\Events\UserJoinEvent;
use App\Models\Events\UserLeaveEvent;
use App\Models\Events\TurnCardEvent;
use App\Session\Cookie;

class IndexController extends Controller
{
    protected $_cookie; 
    
    protected $_user;    
    
    public function __construct()
    {
        parent::__construct();   
        
        try {
            $this->_cookie = new Cookie();
        } catch (\Exception $exc) {
            $this->_cookie = null;
        }    
        
        $this->setRenderParam('title', 'Симулятор карточной игры');
        $this->setLayout('layouts/default');     
    }
    
    public function indexAction()
    {                          
        $request = $this->get('request');
        
        if (!$request->isPost()) {
            return $this->render('pages/index', array('user' => new User));
        }

        $userParams = $request->getPostParam('user');
        
        if (!$userParams) {
            $this->error(404);
        }
        
        $user = new User;
        
        $user->populate($userParams);
        if (!$user->isValid()) {
            $errors = $user->getErrors();

            return $this->render('pages/index', array(
                'user' => $user,
                'errors' => $errors,
            ));
        }

        $user->save();
        
        $this->setCookie($user)
             ->redirect('games/show/' . $user->gameId);
    }
    
    protected function setCookie(User $user)
    {
        $cookie = new Cookie($user);
        $cookie->set();
        
        return $this;
    }
    
    protected function getUser()
    {
        if (!$this->_cookie) {
            return null;
        }
        
        if (!$this->_user) {
            $this->_user = $this->_cookie->getUser();
        }
        
        return $this->_user;
    }
    
    protected function logout()
    {
        $this->_cookie->logout();
        
        return $this;
    }
}