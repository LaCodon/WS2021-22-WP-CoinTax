<?php

namespace Framework\Form;

use Framework\Session;

abstract class RadioInput
{
    public static function render(string $label, string $name, array $options): string
    {
        $inputValidation = Session::getInputValidationResult();
        $oldValue = $inputValidation->getValue($name);

        $errorClass = $inputValidation->getError($name) !== '' ? 'error' : '';

        $errorText = $inputValidation->getError($name);
        $errorSpan = "<span class='$errorClass'>$errorText</span>";

        $variants = '';
        foreach ($options as $key => $option) {
            $checked = $option['value'] == $oldValue ? 'checked' : '';
            if ($oldValue === '' && $key === 0) $checked = 'checked';

            $variants .= <<<EOF
                <div class="input-group-radio">
                    <input class="flexbox $errorClass" id="radio-$name-$key" type="radio" name="$name" value="{$option['value']}" $checked>
                    <label for="radio-$name-$key">{$option['label']}</label>
                </div>
            EOF;
        }

        return <<<EOF
            <div class="search-elem m01">
                <div class="form-elem">
                    <span>$label</span>
                    $variants
                    $errorSpan
                </div>
            </div>
        EOF;
    }
}