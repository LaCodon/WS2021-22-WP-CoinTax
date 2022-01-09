<?php

namespace Core\Coingecko;

use DateTime;
use DateTimeZone;
use Model\Coin;
use ValueError;

/**
 * Wrapper for the coingecko api
 */
final class CoingeckoAPI
{
    public function __construct(
        private string $_endpoint = 'https://api.coingecko.com/api/v3',
    )
    {
    }

    public function getCoin(string $coingeckoId): Coin|null
    {
        $fields = http_build_query([
            'localization' => 'false',
            'tickers' => 'false',
            'market_data' => 'false',
            'community_data' => 'false',
            'developer_data' => 'false',
            'sparkline' => 'false',
        ]);

        $curl = curl_init($this->makeApiPath('/coins/' . $coingeckoId . '?' . $fields));

        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_RETURNTRANSFER => true
        ]);

        $resp = curl_exec($curl);
        if ($resp === false) {
            return null;
        }

        $respObj = json_decode($resp);
        if ($respObj === null || isset($respObj->error)) {
            return null;
        }

        return new Coin(
            strtoupper($respObj->symbol),
            $respObj->name,
            $respObj->image->small,
            $respObj->id,
        );
    }

    /**
     * Get average price of given coin at given day
     * @param Coin $coin
     * @param DateTime $datetime
     * @return string|null
     */
    public function getPriceData(Coin $coin, DateTime $datetime): string|null
    {
        $datetime = $datetime->setTimezone(new DateTimeZone('UTC'))->format('d-m-Y');

        $fields = http_build_query([
            'date' => $datetime,
            'localization' => 'false',
        ]);

        $curl = curl_init($this->makeApiPath('/coins/' . $coin->getCoingeckoId() . '/history?' . $fields));

        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_RETURNTRANSFER => true
        ]);

        $resp = curl_exec($curl);
        if ($resp === false) {
            return null;
        }

        $respObj = json_decode($resp);
        if ($respObj === null || isset($respObj->error)) {
            return null;
        }

        $prices = $respObj->market_data?->current_price;
        if ($prices === null) {
            return null;
        }

        $priceEur = $prices->eur;

        try {
            $priceValidated = bcadd((string)$priceEur, '0');
        } catch (ValueError) {
            return null;
        }

        return $priceValidated;
    }

    private function makeApiPath(string $_endpoint): string
    {
        return $this->_endpoint . $_endpoint;
    }
}