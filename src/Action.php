<?php

namespace JohnBand;

use Illuminate\Support\Arr;
use JohnBand\Traits\RunsAsAcontroller;
use JohnBand\Traits\ResolvesValidation;
use Illuminate\Contracts\Validation\Validator;

abstract class Action
{
    use RunsAsAcontroller,
        ResolvesValidation;

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
        return new static($attributes);
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
     * Pick multiple attributes by listing their keys
     *
     * @param string[]
     * @return array
     */
    public function only() : array
    {
        $data = [];
        $args = func_get_args();
        foreach ($args as $key) {
            $data[$key] = $this->get($key);
        }
        return $data;
    }

    /**
     * Pick all attributes except those listed by their key
     *
     * @param string[]
     * @return array
     */
    public function except() : array
    {
        $args = func_get_args();
        $data = $this->attributes;
        foreach ($args as $key) {
            unset($data[$key]);
        }
        return $data;
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
        if ($this->validator->fails()) {
            throw new ValidationExcpetion($this->validator);
        }

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

        if ($this->hasControllerMessages()) return $this->resolveControllerMessages();

        return $this->messages();
    }
}