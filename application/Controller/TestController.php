<?php

namespace Controller;

use Framework\Exception\ViewNotFound;
use Framework\Response;

final class TestController extends Controller
{

    /**
     * @throws ViewNotFound
     */
    public function HelloworldAction(Response $resp): void
    {
        $resp->setViewVar('test', 'Hallo Welt');

        $response = file_get_contents('https://api.coingecko.com/api/v3/coins/tixl-new/history?date=01-10-2021&localization=false');

        $data = json_decode($response, false, 512,JSON_BIGINT_AS_STRING);

        var_dump_pre($data->market_data->current_price->btc);
        $eurString = number_format($data->market_data->current_price->btc, 30, '.', '');
        var_dump_pre(bcadd($eurString, "0"));

        $resp->renderView('test');
    }

}