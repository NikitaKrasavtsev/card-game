<?php

namespace Framework\Exceptions; 

class NotFoundException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Страница не найдена', 404);
    }
}
