<?php

namespace Framework\Form;

use Framework\Session;

abstract class SelectInput
{
    public static function render(string $label, string $name, array $options = [], bool $required = true): string
    {
        $inputValidation = Session::getInputValidationResult();

        $oldValue = $inputValidation->getValue($name);
        $errorClass = $inputValidation->getError($name) !== '' ? 'error' : '';
        $requiredAttr = $required ? 'required' : '';

        $errorText = $inputValidation->getError($name);
        $errorSpan = "<span class='$errorClass'>$errorText</span>";

        $optionsRender = self::renderOptions($options);

        return <<<EOF
            <div class="form-elem">
                <label for="input-$name">$label</label>
                <input list="datalist-for-input-$name" class="$errorClass" id="input-$name" value="$oldValue" type="text" name="$name" autocomplete="off" data-js="enable-dropdown" $requiredAttr>
                $errorSpan
                <datalist id="datalist-for-input-$name">
                    $optionsRender
                </datalist>
            </div>
            EOF;
    }

    private static function renderOptions(array $options): string
    {
        $result = '';

        foreach ($options as $value => $option) {
            $name = $option['name'];
            $thumbnail = isset($option['thumbnail']) ? '<img class="token-symbol-small" src="' . $option['thumbnail'] . '" alt="' . $value . '">' : '';
            $result .= <<<EOF
                <option value="$value">
                    <div class="flexbox flex-start flex-center flex-gap">
                        $thumbnail
                        <span>$name</span>
                    </div>
                </option>
                EOF;
        }

        return $result;
    }
}