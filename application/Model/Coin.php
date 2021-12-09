<?php

namespace Model;

use Framework\Exception\IdOverrideDisallowed;
use JsonSerializable;

final class Coin implements JsonSerializable
{
    /**
     * @param string $_symbol
     * @param string $_name
     * @param string $_thumbnail_url
     * @param string|null $_coingeckoId
     * @param int $_id
     */
    public function __construct(
        private string      $_symbol,
        private string      $_name,
        private string      $_thumbnail_url,
        private string|null $_coingeckoId,
        private int         $_id = -1,
    )
    {
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
    public function getCoingeckoId(): string
    {
        return $this->_coingeckoId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->_symbol;
    }

    /**
     * @return string
     */
    public function getThumbnailUrl(): string
    {
        return $this->_thumbnail_url;
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