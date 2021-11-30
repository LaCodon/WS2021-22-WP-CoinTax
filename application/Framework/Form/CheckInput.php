<?php

namespace Framework\Form;

use Framework\Session;

abstract class CheckInput
{
    public static function render(string $label, string $name, bool $required = true): string
    {
        $inputValidation = Session::getInputValidationResult();

        $checked = $inputValidation->getValue($name) !== '' ? 'checked' : '';
        $errorClass = $inputValidation->getError($name) !== '' ? 'error' : '';
        $requiredAttr = $required ? 'required' : '';

        $errorText = $inputValidation->getError($name);
        $errorSpan = "<span class='$errorClass'>$errorText</span>";

        return <<<EOF
            <div class="form-elem check-elem">
                <input class="$errorClass inline" id="input-$name" value="1" type="checkbox" name="$name" $requiredAttr $checked>
                <label class="inline" for="input-$name">$label</label>
                $errorSpan
            </div>
            EOF;
    }
}