<?php

namespace App\Models\Events\Formatters;

interface EventFormatter
{
    public function format($event);
}