<?php

namespace Controller;

use Framework\Response;

final class TestController extends Controller
{

    public function HelloworldAction(Response $resp): void
    {
        $resp->test = 'Hallo Welt';

        $resp->renderView('test');
    }

}