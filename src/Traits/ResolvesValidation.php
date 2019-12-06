<?php

namespace JohnBand\Traits;

trait ResolvesValidation
{
    protected function hasControllerRules() : bool
    {
        return method_exists($this, 'controllerRules');
    }

    protected function resolveControllerRules() : array
    {
        return $this->controllerRules($this->rules());
    }

    protected function hasControllerMessages() : bool
    {
        return method_exists($this, 'controllerMessages');
    }

    protected function resolveControllerMessages() : array
    {
        return $this->controllerMessages($this->messages());
    }
}