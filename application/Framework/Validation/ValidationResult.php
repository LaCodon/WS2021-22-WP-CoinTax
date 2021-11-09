<?php

namespace Framework\Validation;

final class ValidationResult
{
    public function __construct(
        private array $_errors = [],
        private array $_values = [],
    )
    {
    }

    /**
     * @param string $inputName
     * @param string $error
     */
    public function setError(string $inputName, string $error): void
    {
        $this->_errors[$inputName] = $error;
    }

    /**
     * @param string $inputName
     * @param string $value
     */
    public function setValue(string $inputName, string $value): void
    {
        $this->_values[$inputName] = $value;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->_errors;
    }

    public function hasErrors(): bool
    {
        return count($this->_errors) > 0;
    }

    public function hasValues(): bool
    {
        return count($this->_values) > 0;
    }

    public function getValue(string $inputName): mixed
    {
        if (isset($this->_values[$inputName])) {
            return $this->_values[$inputName];
        }

        return '';
    }

    public function getError(string $inputName): string
    {
        if (isset($this->_errors[$inputName])) {
            return $this->_errors[$inputName];
        }

        return '';
    }
}