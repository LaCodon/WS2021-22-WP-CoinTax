<?php

namespace Controller;

use Framework\Exception\ViewNotFound;
use Framework\Response;

final class IndexController extends Controller
{

    /**
     * @throws ViewNotFound
     */
    public function Action(Response $resp): void
    {
        if ($resp->isAuthorized()) {
            $resp->redirect($resp->getActionUrl('index', 'dashboard'));
        }

        $resp->setHtmlTitle('Willkommen');
        $resp->renderView('index');
    }

    public function ImpressumAction(Response $resp): void
    {
        $resp->setHtmlTitle('Impressum');
        $resp->renderView('impressum');
    }

    public function PrivacyAction(Response $resp): void
    {
        $resp->setHtmlTitle('Datenschutz');
        $resp->renderView('privacy');
    }

    public function DocumentationAction(Response $resp): void
    {
        $resp->setHtmlTitle('Dokumentation');
        $resp->renderView('documentation');
    }
}