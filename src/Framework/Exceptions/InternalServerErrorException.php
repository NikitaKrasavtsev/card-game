<?php

namespace Framework\Exceptions; 

class InternalServerErrorException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Произошла ошибка', 500);
    }
}
