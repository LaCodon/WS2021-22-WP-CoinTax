<?php

namespace Core\Coingecko;

use Model\Coin;

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

    private function makeApiPath(string $_endpoint): string
    {
        return $this->_endpoint . $_endpoint;
    }
}