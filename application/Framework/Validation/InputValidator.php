<?php

namespace Framework\Validation;

use Framework\Exception\ValidationFailure;

/**
 * This class parses user inputs from the request and validates them according to given rules
 */
abstract class InputValidator
{
    public static function parseAndValidate($inputs): ValidationResult
    {
        $result = new ValidationResult();

        foreach ($inputs as $input) {
            try {
                $val = self::parseAndValidateSingle($input);
                if ($val === null) {
                    if ($input->_required) {
                        throw new ValidationFailure("$input->_readableName darf nicht leer sein");
                    } else {
                        $val = '';
                    }
                }

                $result->setValue($input->_name, $val);
            } catch (ValidationFailure $e) {
                $result->setError($input->_name, $e->getMessage());
            }
        }

        return $result;
    }

    /**
     * @throws ValidationFailure
     */
    private static function parseAndValidateSingle(Input $input): mixed
    {
        $var = filter_input($input->_method, $input->_name, $input->_filter, $input->_options);
        if ($var === null || $var === '') {
            // var was not set
            if ($input->_required) {
                throw new ValidationFailure("$input->_readableName darf nicht leer sein");
            }
        } elseif ($var === false) {
            // filter failed
            throw new ValidationFailure("$input->_readableName hat das falsche Format");
        }

        return $var;
    }
}