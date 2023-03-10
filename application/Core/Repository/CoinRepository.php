<?php

namespace Core\Repository;

use Framework\Exception\IdOverrideDisallowed;
use Framework\Exception\UniqueConstraintViolation;
use Model\Coin;
use PDO;
use PDOException;

/**
 * Repository for accessing the SQL coin table
 */
final class CoinRepository
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
    public function insert(Coin $coin): bool
    {
        if ($coin->getId() !== -1) {
            // coin is already in database
            return false;
        }

        $symbol = $coin->getSymbol();
        $name = $coin->getName();
        $coingeckId = $coin->getCoingeckoId();
        $thumbnailUrl = $coin->getThumbnailUrl();

        $stmt = $this->_pdo->prepare('INSERT INTO coin (symbol, name, coingecko_id, thumbnail_url) VALUES (:symbol, :name, :coingeckoId, :thumbnailUrl)');
        $stmt->bindParam(":symbol", $symbol);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":coingeckoId", $coingeckId);
        $stmt->bindParam(":thumbnailUrl", $thumbnailUrl);

        try {
            $res = $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                // violation against unique constraint aka symbol already exists for another coin
                throw new UniqueConstraintViolation();
            } else {
                throw $e;
            }
        }

        $coin->setId($this->_pdo->lastInsertId());

        return $res;
    }

    /**
     * Load coin with given id from database
     * @param int|null $id
     * @return Coin|null
     */
    public function get(int|null $id): Coin|null
    {
        if ($id === null) {
            return null;
        }

        $stmt = $this->_pdo->prepare('SELECT coin_id, symbol, name, coingecko_id, thumbnail_url FROM coin WHERE coin_id = :coinId LIMIT 1');
        $stmt->bindParam(':coinId', $id, PDO::PARAM_INT);
        if ($stmt->execute() === false) {
            return null;
        }

        return $this->makeCoin($stmt->fetchObject());
    }

    /**
     * Load all coins from the database
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->_pdo->prepare('SELECT coin_id, symbol, name, coingecko_id, thumbnail_url FROM coin');
        if ($stmt->execute() === false) {
            return [];
        }

        $result = [];

        while (($obj = $stmt->fetchObject()) !== false) {
            $result[] = $this->makeCoin($obj);
        }

        return $result;
    }

    /**
     * @param string $symbol
     * @return Coin|null
     */
    public function getBySymbol(string $symbol): Coin|null
    {
        $stmt = $this->_pdo->prepare('SELECT coin_id, symbol, name, coingecko_id, thumbnail_url FROM coin WHERE symbol = :symbol LIMIT 1');
        $stmt->bindParam(':symbol', $symbol);
        if ($stmt->execute() === false) {
            return null;
        }

        return $this->makeCoin($stmt->fetchObject());
    }

    /**
     * Returns an array of coins which symbol or name contains the given partial symbol
     * @param string $partialSymbol
     * @return array
     */
    public function getByQuery(string $partialSymbol): array
    {
        str_replace('%', '', $partialSymbol);
        $partialSymbol = "%$partialSymbol%";

        $partialSymbolLower = strtolower($partialSymbol);

        $stmt = $this->_pdo->prepare('SELECT * FROM coin WHERE symbol LIKE :symbol OR coingecko_id LIKE :lowerSymbol ORDER BY symbol');
        $stmt->bindParam(':symbol', $partialSymbol);
        $stmt->bindParam(':lowerSymbol', $partialSymbolLower);

        if ($stmt->execute() === false) {
            return [];
        }

        $result = [];

        while (($obj = $stmt->fetchObject()) !== false) {
            $result[] = $this->makeCoin($obj);
        }

        return $result;
    }

    /**
     * Returns a list of all coins ever used by the given user over all transactions
     * @param int $userId
     * @return array|null
     */
    public function getUniqueCoinsByUserId(int $userId): array|null
    {
        $stmt = $this->_pdo->prepare('SELECT c.* FROM coin c
                                                JOIN transaction t ON t.coin_id = c.coin_id
                                            WHERE t.user_id = :userId
                                            GROUP BY c.coin_id');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        if ($stmt->execute() === false) {
            return null;
        }

        $result = [];

        while (($obj = $stmt->fetchObject()) !== false) {
            $result[] = $this->makeCoin($obj);
        }

        return $result;
    }

    /**
     * Create a coin from a PDO result object
     * @param object|bool $resultObj
     * @return Coin|null
     */
    public function makeCoin(object|bool $resultObj): Coin|null
    {
        if ($resultObj === false) {
            return null;
        }

        return new Coin(
            $resultObj->symbol,
            $resultObj->name,
            $resultObj->thumbnail_url,
            $resultObj->coingecko_id,
            $resultObj->coin_id,
        );
    }
}