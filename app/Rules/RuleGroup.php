<?php

namespace App\Rules;

abstract class RuleGroup{

    public static final function getRules(): array
    {
        return static::$rules;
    }

}