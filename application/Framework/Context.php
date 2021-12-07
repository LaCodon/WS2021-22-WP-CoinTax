<?php

namespace Framework;

use Core\Repository\CoinRepository;
use Core\Repository\OrderRepository;
use Core\Repository\PaymentInfoRepository;
use Core\Repository\PriceRepository;
use Core\Repository\TransactionRepository;
use Core\Repository\UserRepository;

final class Context
{
    /**
     * @param CoinRepository $_coinRepo
     * @param OrderRepository $_orderRepo
     * @param PaymentInfoRepository $_paymentInfoRepo
     * @param PriceRepository $_priceRepo
     * @param TransactionRepository $_transactionRepo
     * @param UserRepository $_userRepo
     */
    public function __construct(
        private CoinRepository        $_coinRepo,
        private OrderRepository       $_orderRepo,
        private PaymentInfoRepository $_paymentInfoRepo,
        private PriceRepository       $_priceRepo,
        private TransactionRepository $_transactionRepo,
        private UserRepository        $_userRepo,
    )
    {
    }

    /**
     * @return CoinRepository
     */
    public function getCoinRepo(): CoinRepository
    {
        return $this->_coinRepo;
    }

    /**
     * @return OrderRepository
     */
    public function getOrderRepo(): OrderRepository
    {
        return $this->_orderRepo;
    }

    /**
     * @return PaymentInfoRepository
     */
    public function getPaymentInfoRepo(): PaymentInfoRepository
    {
        return $this->_paymentInfoRepo;
    }

    /**
     * @return PriceRepository
     */
    public function getPriceRepo(): PriceRepository
    {
        return $this->_priceRepo;
    }

    /**
     * @return TransactionRepository
     */
    public function getTransactionRepo(): TransactionRepository
    {
        return $this->_transactionRepo;
    }

    /**
     * @return UserRepository
     */
    public function getUserRepo(): UserRepository
    {
        return $this->_userRepo;
    }
}