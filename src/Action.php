<?php

namespace JohnBand;

abstract class Action
{
    protected $data;

    /** @var Validator */
    protected $validator;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public static function make($data) : self
    {
        return new self($data);
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function get($key)
    {
        return Arr::get($this->data, $key, null);
    }

    public function all()
    {
        return $this->data;
    }

    public function hasRules() : bool
    {
        return method_exists($this, 'rules');
    }

    public function hasMessages() : bool
    {
        return method_exists($this, 'messages');
    }

    public function run()
    {
        $this->validate();

        return $this->execute();
    }

    protected function validate()
    {
        if (! $this->hasRules) return;

        $this->validator = ValidatorFacade::make(
            $this->data,
            $this->rules(),
            $this->hasMessages() ? $this->messages : []
        );

        $this->validator->validate();
    }
}