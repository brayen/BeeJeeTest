<?php

namespace App\Enums;


enum StatusEnum: string
{
    case Todo = 'todo';
    case Done = 'done';

    /**
     * @return string
     */
    public function getStatusStyle(): string
    {
        return match ($this) {
            self::Todo  => 'info',
            self::Done  => 'success',
        };
    }
}
