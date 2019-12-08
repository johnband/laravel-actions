<?php

namespace JohnBand\Traits;

use Illuminate\Support\Facades\Validator;

trait ResolvesValidation
{
    /** @var ValidationExcpetion */
    protected $validationException;

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

    protected function validate()
    {
        if (! $this->hasRules()) return;

        $this->validator = Validator::make(
            $this->attributes,
            $this->resolveRules(),
            $this->resolveMessages()
        );

        $this->validator->validate();
    }
}