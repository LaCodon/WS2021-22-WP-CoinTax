<?php

namespace Controller;

use Core\Repository\CoinRepository;
use Framework\Exception\ViewNotFound;
use Framework\Response;

final class OrderController extends Controller
{

    /**
     * @throws ViewNotFound
     */
    public function Action(Response $resp): void
    {
        $this->abortIfUnauthorized();

        $resp->renderView('index');
    }

    /**
     * @throws ViewNotFound
     */
    public function AddAction(Response $resp): void
    {
        $this->abortIfUnauthorized();

        $coinRepo = new CoinRepository($this->db());
        $coins = $coinRepo->getAll();

        $coinOptions = [];

        foreach ($coins as $coin) {
            $coinOptions[$coin->getSymbol()] = [
                'name' => $coin->getName(),
                'thumbnail' => $coin->getThumbnailUrl(),
            ];
        }

        $resp->setViewVar('coin_options', $coinOptions);

        $resp->renderView('add');
    }

    public function AddDoAction(Response $resp): void
    {
        $this->abortIfUnauthorized();
        $this->expectMethodPost();

        var_dump_pre($_POST);
    }

}