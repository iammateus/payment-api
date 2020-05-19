<?php

namespace App\Validators;

use InvalidArgumentException;
use Illuminate\Validation\Validator;
use Illuminate\Validation\ValidationException;

class DocumentValidator {

    /**
     * Validates a document [CPF, CNPJ]
     *
     * @param string $attribute
     * @param string $value
     * @param array $parameters 
     * @param Validator $validator
     * @return bool
     */
    public function validate(string $attribute, string $value, array $parameters = [], Validator $validator)
    {
        $typePath = explode('.', $parameters[0]);

        $type = $validator->getData();
        foreach ($typePath as $path) {
            if (empty($type[$path])){
                throw new InvalidArgumentException("The given path of document type is not valid.", 1);
            }

            $type = $type[$path];
        }

        if(! in_array($type, [ 'CPF', 'CNPJ' ]) ){
            
        }

        if($type === 'CPF'){
            return $this->cpf($value);
        }
        
        return $this->cnpj($value);
        
    }

    /**
     * Validates a CPF
     *
     * @param string $cpf
     * @return boolean
     */
    public function cpf(string $cpf): bool
    {
        $cpfLen = strlen($cpf);

        /**
         * A CPF is always 11 digits
         */
        if ($cpfLen === 11) {

            /**
             * First step of validation:
             * 
             * Checking if first verifying digit is valid
             */ 
            $sumResult = 0;

            for ($i = 0; $i < ($cpfLen - 2); $i++) { 
                $sumResult+= intval(substr($cpf, $i, 1)) * (10 - $i);
            }

            $restOfDivision = ($sumResult * 10) % 11;

            if ($restOfDivision === 10 || $restOfDivision === 11) {
                $restOfDivision = 0;
            }

            if ($restOfDivision === intval(substr($cpf, $cpfLen-2, 1))) {
                
                 /**
                 * Second step of validation:
                 * 
                 * Checking if second verifying digit is valid
                 */ 
                $sumResult = 0;
                
                for ($i = 0; $i < ($cpfLen - 1); $i++) { 
                    $sumResult+= intval(substr($cpf, $i, 1)) * (11 - $i);
                }

                $restOfDivision = ($sumResult * 10) % 11;

                if ($restOfDivision === 10 || $restOfDivision === 11) {
                    $restOfDivision = 0;
                }

                if ($restOfDivision === intval(substr($cpf, $cpfLen-1, 1))) {

                    if(
                           $cpf !== "00000000000" 
                        && $cpf !== "11111111111"
                        && $cpf !== "22222222222"
                        && $cpf !== "33333333333"
                        && $cpf !== "44444444444"
                        && $cpf !== "55555555555"
                        && $cpf !== "66666666666"
                        && $cpf !== "77777777777"
                        && $cpf !== "88888888888"
                        && $cpf !== "99999999999"
                    ){

                        return true;

                    }
                
                }

            }

        }

        return false;
    
    }
   
    /**
     * Validates a CNPJ
     *
     * @param string $cnpj
     * @return boolean
     */
    public function cnpj(string $cnpj): bool
    {
        $cnpjLen = strlen($cnpj);

        if($cnpjLen != 14){
            return false;
        }

        /**
         * First step of validation:
         */ 
        $total = 0;
        $multiplier = 2;
        for ($count = 11; $count >= 0; $count--) {
            if($multiplier === 10){
                $multiplier = 2;
            }

            $total += $multiplier * (int) substr($cnpj, $count, 1);
            $multiplier++;
        }

        $mod = $total % 11;

        $verifier = 11 - $mod;

        if(in_array($mod, [0, 1])){
            $verifier = 0;
        }

        $strVerifier = (int) substr($cnpj, 12, 1);
        if($verifier !== $strVerifier){
            return false;
        }

        /**
         * Second step of validation:
         */ 
        $total = 0;
        $multiplier = 2;
        for ($count = 12; $count >= 0; $count--) {
            if($multiplier === 10){
                $multiplier = 2;
            }

            $total += $multiplier * (int) substr($cnpj, $count, 1);
            $multiplier++;
        }

        $mod = $total % 11;

        $verifier = 11 - $mod;

        if(in_array($mod, [0, 1])){
            $verifier = 0;
        }

        $strVerifier = (int) substr($cnpj, 13, 1);
        if($verifier !== $strVerifier){
            return false;
        }

        return true;
    }
}