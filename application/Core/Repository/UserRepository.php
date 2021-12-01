<?php

namespace Core\Repository;

use Framework\Exception\IdOverrideDisallowed;
use Framework\Exception\UniqueConstraintViolation;
use \PDO;
use Model\User;
use PDOException;

final class UserRepository
{
    public function __construct(
        private PDO $_pdo,
    )
    {
    }

    /**
     * @throws IdOverrideDisallowed
     * @throws UniqueConstraintViolation
     */
    public function insert(User $user): bool
    {
        if ($user->getId() !== -1) {
            // user is already in database
            return false;
        }

        $email = $user->getEmail();
        $firstname = $user->getFirstName();
        $lastname = $user->getLastName();
        $passwordHash = $user->getPasswordHash();

        $stmt = $this->_pdo->prepare('INSERT INTO user (email, first_name, last_name, password) VALUES (:email, :first_name, :last_name, :password)');
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":first_name", $firstname);
        $stmt->bindParam(":last_name", $lastname);
        $stmt->bindParam(":password", $passwordHash);

        try {
            $res = $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                // violation against unique constraint aka email already exists for another user
                throw new UniqueConstraintViolation();
            } else {
                throw $e;
            }
        }

        $user->setId($this->_pdo->lastInsertId());

        return $res;
    }

    /**
     * Update the given user in the database via its id
     * @param User $user
     * @return bool
     * @throws UniqueConstraintViolation
     */
    public function update(User $user): bool
    {
        if ($user->getId() === -1) {
            // user has to be inserted at first
            return false;
        }

        $userId = $user->getId();
        $email = $user->getEmail();
        $firstname = $user->getFirstName();
        $lastname = $user->getLastName();
        $passwordHash = $user->getPasswordHash();

        $stmt = $this->_pdo->prepare('UPDATE user SET email = :email, first_name = :first_name, last_name = :last_name, password = :password WHERE user_id = :userId');
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":first_name", $firstname);
        $stmt->bindParam(":last_name", $lastname);
        $stmt->bindParam(":password", $passwordHash);

        try {
            $res = $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                // violation against unique constraint aka email already exists for another user
                throw new UniqueConstraintViolation();
            } else {
                throw $e;
            }
        }

        return $res;
    }

    /**
     * Load user with given id from database
     * @param int $id
     * @return User|null
     */
    public function get(int $id): User|null
    {
        $stmt = $this->_pdo->prepare('SELECT user_id, email, first_name, last_name, password FROM user WHERE user_id = :userId LIMIT 1');
        $stmt->bindParam(':userId', $id, PDO::PARAM_INT);
        if ($stmt->execute() === false) {
            return null;
        }

        return $this->makeUser($stmt->fetchObject());
    }

    /**
     * Load user with given email from database
     * @param string $email
     * @return User|null
     */
    public function getByEmail(string $email): User|null
    {
        $stmt = $this->_pdo->prepare('SELECT user_id, email, first_name, last_name, password FROM user WHERE email = :email LIMIT 1');
        $stmt->bindParam(':email', $email);
        if ($stmt->execute() === false) {
            return null;
        }

        return $this->makeUser($stmt->fetchObject());
    }

    /**
     * Create a user from a PDO result object
     * @param object|bool $resultObj
     * @return User|null
     */
    private function makeUser(object|bool $resultObj): User|null
    {
        if ($resultObj === false) {
            return null;
        }

        return new User(
            $resultObj->first_name,
            $resultObj->last_name,
            $resultObj->email,
            $resultObj->password,
            $resultObj->user_id,
        );
    }
}