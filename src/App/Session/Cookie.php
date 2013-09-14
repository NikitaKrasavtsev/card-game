<?php

namespace App\Session; 

use App\Models\User;

class Cookie
{
    const NAME = 'uid';   
    
    const CYPHER = MCRYPT_RIJNDAEL_256;
    const MODE   = MCRYPT_MODE_CBC;
    const KEY    = 'aR48seEn94W';              
    
    private $_user;
    
    public function __construct(User $user = null)
    {        
        if ($user) {
            $this->_user = $user;            
            
            return;
        }
        
        if (array_key_exists(self::NAME, $_COOKIE)) {
            
            $this->extract($_COOKIE[self::NAME])
                 ->validate();
            
            return;
        } 
        
        throw new \Exception('Cookie not found');
    }    
    
    public function set()
    {
        $cookie = $this->pack();

        setcookie(self::NAME, $cookie, 0, '/');
        
        return $this;
    }
    
    public function logout()
    {
        setcookie(self::NAME, "", time() - 3600, '/');
        
        return $this;
    }
    
    public function getUser()
    {
        return $this->_user;
    }
    
    private function validate()
    {   
        if (!$this->_user) {
            throw new \Exception('Invalid user id');
        }
    }
    
    private function pack()
    {
        $cookieValue = $this->_user->id;
        
        return $this->encrypt($cookieValue);
    }
    
    private function extract($cookieValue)
    {
        $userId = $this->decrypt($cookieValue);                 
        
        if (!$userId) {
            throw new \Exception('Invalid format cookie');
        }        
        
        $this->_user = User::find($userId);

        return $this;
    }
    
    private function encrypt($plaintext)
    {        
        $td = mcrypt_module_open(self::CYPHER, '', self::MODE, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, self::KEY, $iv);
        $crypttext = mcrypt_generic($td, $plaintext);
        mcrypt_generic_deinit($td);
        
        return base64_encode($iv.$crypttext); 
    }
    
    private function decrypt($crypttext)
    {
        $crypttext = base64_decode($crypttext);
        $plaintext = '';
        $td = mcrypt_module_open(self::CYPHER, '', self::MODE, '');
        $ivsize = mcrypt_enc_get_iv_size($td);
        $iv = substr($crypttext, 0, $ivsize);
        $crypttext = substr($crypttext, $ivsize);
        if ($iv) {
            mcrypt_generic_init($td, self::KEY, $iv);
            $plaintext = mdecrypt_generic($td, $crypttext);
        }
        
        return trim($plaintext);
    }
}