<?php

namespace Model;

use Framework\Exception\IdOverrideDisallowed;

final class User
{
    /**
     * @param string $_firstName
     * @param string $_lastName
     * @param string $_email
     * @param string $_passwordHash
     * @param int $_id
     */
    public function __construct(
        private string $_firstName,
        private string $_lastName,
        private string $_email,
        private string $_passwordHash,
        private int    $_id = -1
    )
    {
    }

    /**
     * @param int $id
     * @throws IdOverrideDisallowed
     */
    public function setId(int $id): void
    {
        if ($this->_id !== -1) {
            throw new IdOverrideDisallowed();
        }

        $this->_id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->_id;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->_firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->_lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->_email;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->_passwordHash;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->_firstName = $firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->_lastName = $lastName;
    }
}