<?php

namespace JohnBand;

use Illuminate\Support\Arr;
use JohnBand\Traits\RunsAsAcontroller;
use JohnBand\Traits\ResolvesValidation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

abstract class Action
{
    use RunsAsAcontroller, ResolvesValidation;

    /** @var array */
    protected $attributes;

    /** @var Validator */
    protected $validator;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Instaciating an Action the static way
     *
     * @param array $attributes
     * @return Action
     */
    public static function make(array $attributes) : self
    {
        return new self($attributes);
    }

    /**
     * Set an attribute by its key
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Get an attribute by key
     *
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return Arr::get($this->attributes, $key, null);
    }

    /**
     * Retreive all attributes
     *
     * @return array
     */
    public function all() : array
    {
        return $this->attributes;
    }

    /**
     * Runs the action
     *
     * @return mixed
     */
    public function run()
    {
        $this->validate();

        return $this->execute();
    }

    /**
     * Checks if validation rules have been set for the action
     *
     * @return bool
     */
    protected function hasRules() : bool
    {
        return method_exists($this, 'rules');
    }

    protected function resolveRules() : array
    {
        if (! $this->hasRules()) return [];

        if ($this->hasControllerRules()) {
            return $this->resolveControllerRules();
        }

        return $this->rules();
    }

    /**
     * Checks if messages have been set, should the validation fail
     *
     * @return bool
     */
    protected function hasMessages() : bool
    {
        return method_exists($this, 'messages');
    }

    protected function resolveMessages() : array
    {
        if (! $this->hasMessages()) return [];

        if ($this->hasControllerMessages()) {
            return $this->resolveControllerMessages();
        }

        return $this->messages();
    }

    protected function validate()
    {
        if (! $this->hasRules()) return;

        $this->validator = ValidatorFacade::make(
            $this->attributes,
            $this->resolveRules(),
            $this->resolveMessages()
        );

        $this->validator->validate();
    }
}