<?php

namespace Framework\Html;

use DateTimeZone;
use Model\Coin;

abstract class Transaction
{
    /**
     * Render a single transaction in a card element
     * @param int $orderId
     * @param \Model\Transaction $transaction
     * @param Coin $coin
     * @param string $eurValue
     * @param bool $isFee
     * @return string The HTML
     */
    public static function render(int $orderId, \Model\Transaction $transaction, Coin $coin, string $eurValue, bool $isFee = false): string
    {
        $arrow = $transaction->getType() === \Model\Transaction::TYPE_SEND ? 'arrow_upward' : 'arrow_downward';
        $color = $transaction->getType() === \Model\Transaction::TYPE_SEND ? 'red' : 'green';
        $date = $transaction->getDatetimeUtc()->setTimezone(new DateTimeZone('Europe/Berlin'))->format('d.m.Y H:i');
        $coinValue = format_number($transaction->getValue(), maxDecimals: 8);
        $fiatValue = format_number($eurValue, maxDecimals: 2);

        $feeString = $isFee ? '<span class="hint">Gebühr</span>' : '';

        return <<<EOF
            <div class="w12 flexbox card">
                <div class="flexbox w2 flex-col flex-gap">
                    <a href="../order/details?id=$orderId"><span class="material-icons swap-icon $color">$arrow</span></a>
                    <span class="text-light">$date Uhr</span>
                </div>

                <div class="flexbox w8 flexbox-center">
                    <div class="flexbox flexbox-center flex-col w2 flex-gap">
                        <div><img class="token-symbol"
                                  src="{$coin->getThumbnailUrl()}"
                                  alt="{$coin->getName()}"></div>
                        <div class="text-light text-center">
                            $coinValue
                            {$coin->getSymbol()}<br>
                            $feeString
                        </div>
                    </div>
                </div>

                <div class="w2">
                    <div class="text-light">
                        Wert: $fiatValue EUR
                    </div>
                </div>
            </div>
            EOF;
    }
}