<?php

namespace Model;

use Framework\Exception\IdOverrideDisallowed;

final class PaymentInfo
{

    /**
     * @param int $_userId
     * @param string $_iban
     * @param string $_bic
     * @param int $_year
     * @param bool $_fulfilled
     * @param bool $_failed
     * @param int $_id
     */
    public function __construct(
        private int    $_userId,
        private string $_iban,
        private string $_bic,
        private int    $_year,
        private bool   $_fulfilled = false,
        private bool   $_failed = false,
        private int    $_id = -1,
    )
    {
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->_userId;
    }

    /**
     * @return string
     */
    public function getBic(): string
    {
        return $this->_bic;
    }

    /**
     * @return string
     */
    public function getIban(): string
    {
        return $this->_iban;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->_year;
    }

    /**
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->_failed;
    }

    /**
     * @return bool
     */
    public function isFulfilled(): bool
    {
        return $this->_fulfilled;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->_id;
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
}