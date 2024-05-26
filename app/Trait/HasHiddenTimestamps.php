<?php

namespace App\Trait;

/**
 * Esconde os atributos created_at e updated_at
 */
trait HasHiddenTimestamps
{
    public function __construct()
    {
        $this->hidden = array_merge($this->hidden, ['created_at', 'updated_at']);
    }
}
