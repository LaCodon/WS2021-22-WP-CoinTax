<?php

namespace Framework;

use Framework\Exception\SessionsStartFailed;
use Framework\Validation\ValidationResult;
use Model\User;

/**
 * Class for session handling and recreation
 */
abstract class Session
{
    /**
     * Start the usage of a session
     * @throws SessionsStartFailed
     */
    public static function start(): void
    {
        session_name('clientId');

        if (session_start() === false) {
            throw new SessionsStartFailed();
        }
    }

    /**
     * Generates a new session id
     * @param bool $delete_old_session
     */
    public static function regenerateId(bool $delete_old_session = true)
    {
        session_regenerate_id($delete_old_session);
    }

    /**
     * Destroy the current session and all its contents
     */
    public static function destroySession()
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }

    /**
     * Set the ValidationResult for usage in the frontend
     * @param ValidationResult $result
     */
    public static function setInputValidationResult(ValidationResult $result): void
    {
        $_SESSION['framework_input_validation'] = $result;
    }

    /**
     * Get the result of the last input validation
     * @return ValidationResult
     */
    public static function getInputValidationResult(): ValidationResult
    {
        if (isset($_SESSION['framework_input_validation'])) {
            return $_SESSION['framework_input_validation'];
        }

        return new ValidationResult();
    }

    /**
     * Returns true if the session already has a validation result
     * @return bool
     */
    public static function hasNonEmptyInputValidationResult(): bool
    {
        return isset($_SESSION['framework_input_validation']) && $_SESSION['framework_input_validation']->hasValues();
    }

    /**
     * Remove the last ValidationResult
     */
    public static function clearInputValidationResult(): void
    {
        $_SESSION['framework_input_validation'] = new ValidationResult();
    }

    /**
     * @param User $user
     */
    public static function setAuthorizedUser(User $user): void
    {
        $_SESSION['framework_authorized_user'] = $user;
    }

    /**
     * @param array $filter
     */
    public static function setCurrentFilter(array $filter): void
    {
        $_SESSION['cointax_filter'] = http_build_query($filter);
    }

    /**
     * @return string
     */
    public static function getCurrentFilterQuery(): string
    {
        if (isset($_SESSION['cointax_filter'])) {
            return $_SESSION['cointax_filter'];
        }

        return '';
    }

    /**
     * @return User|null
     */
    public static function getAuthorizedUser(): User|null
    {
        if (isset($_SESSION['framework_authorized_user'])) {
            return $_SESSION['framework_authorized_user'];
        }

        return null;
    }
}