<?php

namespace Core\Calc;

use Core\Repository\CoinRepository;
use Core\Repository\PriceRepository;
use DateTime;
use Model\Coin;
use Model\Transaction;

final class PriceConverter
{
    const EUR_COIN_SYMBOL = 'EUR';

    private PriceRepository $_priceRepo;
    private CoinRepository $_coinRepo;

    /**
     * @param \PDO $_pdo
     */
    public function __construct(
        private \PDO $_pdo,
    )
    {
        $this->_priceRepo = new PriceRepository($this->_pdo);
        $this->_coinRepo = new CoinRepository($this->_pdo);
    }

    /**
     * Get the EUR value of the given transaction pair.
     * If one transaction is already in EUR, use this value. Otherwise, search the database and coingecko.
     * If no price can be found, this method returns '0.0'
     * @param Transaction $baseTransaction
     * @param Transaction $quoteTransaction
     * @param Coin|null $baseCoin
     * @param Coin|null $quoteCoin
     * @return string
     */
    public function getEurValueApiOptional(Transaction $baseTransaction, Transaction $quoteTransaction, Coin|null $baseCoin = null, Coin|null $quoteCoin = null): string
    {
        if ($baseCoin === null) {
            $baseCoin = $this->_coinRepo->get($baseTransaction->getCoinId());
        }

        if ($quoteCoin === null) {
            $quoteCoin = $this->_coinRepo->get($quoteTransaction->getCoinId());
        }

        if ($baseCoin->getSymbol() === self::EUR_COIN_SYMBOL) {
            return $baseTransaction->getValue();
        }

        if ($quoteCoin->getSymbol() === self::EUR_COIN_SYMBOL) {
            return $quoteTransaction->getValue();
        }

        $price = $this->_priceRepo->get($quoteCoin, $baseTransaction->getDatetimeUtc());
        if ($price === null) {
            return '0.0';
        }

        return bcmul($price, $quoteTransaction->getValue());
    }

    /**
     * Same as getEurValueApiOptional but for only one transaction
     * @param Transaction $transaction
     * @param Coin|null $coin
     * @return string
     */
    public function getEurValueApiOptionalSingle(Transaction $transaction, Coin|null $coin = null): string
    {
        if ($coin === null) {
            $coin = $this->_coinRepo->get($transaction->getCoinId());
        }

        if ($coin->getSymbol() === self::EUR_COIN_SYMBOL) {
            return $transaction->getValue();
        }

        $price = $this->_priceRepo->get($coin, $transaction->getDatetimeUtc());
        if ($price === null) {
            return '0.0';
        }

        return bcmul($price, $transaction->getValue());
    }

    /**
     * Get value of given amount of coin for a specific point in time. Searches DB for price information or get
     * price data from coin gecko.
     * @param string $value
     * @param Coin $coin
     * @param DateTime $dateTime
     * @return string
     */
    public function getEurValuePlainApiOptional(string $value, Coin $coin, DateTime $dateTime): string
    {
        if ($coin->getSymbol() === self::EUR_COIN_SYMBOL) {
            return $value;
        }

        $price = $this->_priceRepo->get($coin, $dateTime);
        if ($price === null) {
            return '0.0';
        }

        return bcmul($price, $value);
    }
}