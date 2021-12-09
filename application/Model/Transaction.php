<?php

namespace Model;

use DateTime;
use DateTimeZone;
use Framework\Exception\IdOverrideDisallowed;
use JsonSerializable;

final class Transaction implements JsonSerializable
{
    const TYPE_RECEIVE = 'receive';
    const TYPE_SEND = 'send';

    /**
     * @param int $_userId
     * @param DateTime $_datetimeUtc
     * @param string $_type
     * @param int $_coinId
     * @param string $_value
     * @param int $_id
     */
    public function __construct(
        private int      $_userId,
        private DateTime $_datetimeUtc,
        private string   $_type,
        private int      $_coinId,
        private string   $_value,
        private int      $_id = -1,
    )
    {
    }

    /**
     * @return int
     */
    public function getCoinId(): int
    {
        return $this->_coinId;
    }

    /**
     * @return DateTime
     */
    public function getDatetimeUtc(): DateTime
    {
        $this->_datetimeUtc->setTimezone(new DateTimeZone('UTC'));
        return $this->_datetimeUtc;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->_type;
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
    public function getValue(): string
    {
        return $this->_value;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->_id;
    }

    /**
     * @param int|null $id
     * @throws IdOverrideDisallowed
     */
    public function setId(int|null $id): void
    {
        if ($id === null) {
            return;
        }

        if ($this->_id !== -1) {
            throw new IdOverrideDisallowed();
        }

        $this->_id = $id;
    }

    public function jsonSerialize(): array
    {
        $json = array();

        foreach ($this as $key => $value) {
            $key = str_replace('_', '', $key);
            $json[$key] = $value;
        }

        return $json;
    }
    
}