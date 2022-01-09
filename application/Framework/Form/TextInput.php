<?php

namespace Framework\Form;

use Framework\Session;

abstract class TextInput
{
    /**
     * Renders an HTML input of the given $type (text, email, passwort, ...)
     * For type=checkbox and type=radio there are specialised classes, use them instead!
     * @param string $label
     * @param string $name
     * @param string $type
     * @param bool $required
     * @param string $placeholder
     * @param string $pattern
     * @return string The HTML
     */
    public static function render(string $label, string $name, string $type = 'text', bool $required = true, string $placeholder = '', string $pattern = ''): string
    {
        $inputValidation = Session::getInputValidationResult();

        $oldValue = $type !== 'password' ? $inputValidation->getValue($name) : '';
        $errorClass = $inputValidation->getError($name) !== '' ? 'error' : '';
        $requiredAttr = $required ? 'required' : '';
        $pattern = $pattern !== '' ? "pattern='$pattern'" : '';

        $errorText = $inputValidation->getError($name);
        $errorSpan = "<span class='$errorClass'>$errorText</span>";

        return <<<EOF
            <div class="form-elem">
                <label for="input-$name">$label</label>
                <input class="$errorClass" id="input-$name" value="$oldValue" type="$type" name="$name" placeholder="$placeholder" $pattern $requiredAttr>
                $errorSpan
            </div>
            EOF;
    }
}