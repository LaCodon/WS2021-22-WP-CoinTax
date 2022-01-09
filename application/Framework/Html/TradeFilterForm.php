<?php

namespace Framework\Html;

use Core\Repository\OrderRepository;
use Framework\Form\RadioInput;
use Framework\Form\SelectInput;
use Framework\Form\TextInput;
use Framework\Response;

abstract class TradeFilterForm
{
    /**
     * Render a search bar for trades and transactions
     * @param Response $resp
     * @return string The HTML
     */
    public static function render(Response $resp): string
    {
        $fromInput = TextInput::render('Von:', 'from', 'datetime-local', false);
        $toInput = TextInput::render('Bis:', 'to', 'datetime-local', false);
        $selectInput = SelectInput::render('Token:', 'token', $resp->getViewVar('coin_options'), false);
        $sortCriteria = RadioInput::render('Sortieren nach:', 'sortAfter', [
            ['label' => 'Datum', 'value' => OrderRepository::SORT_DATE],
            ['label' => 'Gesendete Menge', 'value' => OrderRepository::SORT_SEND_AMOUNT],
            ['label' => 'Empfangene Menge', 'value' => OrderRepository::SORT_RECEIVE_AMOUNT],
        ]);
        $sortDirection = RadioInput::render('', 'sortDirection', [
            ['label' => '<span class="material-icons label-icon">north</span>', 'value' => OrderRepository::SORT_DESC],
            ['label' => '<span class="material-icons label-icon">south</span>', 'value' => OrderRepository::SORT_ASC],
        ]);
        $action = $resp->getActionUrl('index');

        return <<<EOF
            <form method="GET" action="$action">
                <section class="flexbox flexbox-center">
                    <div class="w12 flexbox flex-start">
                        <div class="flexbox w10">
                            <div class="search-elem w3">
                                $fromInput
                            </div>
                            <div class="search-elem w3">
                                $toInput
                            </div>
                            <div class="search-elem w4">
                                $selectInput
                            </div>
                        </div>
                        <div class="flexbox flex-end w2">
                            <button class="btn grey flexbox" type="submit"><span class="material-icons">filter_alt</span>&nbsp;
                                Filtern / Sortieren
                            </button>
                        </div>
                    </div>
                    <div class="w12 m01 flexbox flex-end flex-gap">
                        $sortCriteria
                        $sortDirection
                    </div>
                </section>
            </form>
            EOF;

    }
}