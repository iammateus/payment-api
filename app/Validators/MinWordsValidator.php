<?php

namespace App\Validators;

use Illuminate\Validation\Validator;

class MinWordsValidator {

    public function validate(string $attribute, string $value, array $parameters = [], Validator $validator)
    {
        $min = (int) $parameters[0];
        $explodedWords = explode(' ', $value);

        $validator->addReplacer('min_words', 
            function($message, $attribute, $rule, $parameters) use ($min){
                return \str_replace(':min', $min, $message);
            }
        );

        return count($explodedWords) >= $min;
    }

}