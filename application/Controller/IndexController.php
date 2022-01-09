<?php

namespace Controller;

use Framework\Exception\ViewNotFound;
use Framework\Response;

/**
 * Controller for / and /index
 */
final class IndexController extends Controller
{

    /**
     * Endpoint for GET / and /index/
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

    /**
     * Endpoint for /index/impressum
     * @param Response $resp
     * @throws ViewNotFound
     */
    public function ImpressumAction(Response $resp): void
    {
        $resp->setHtmlTitle('Impressum');
        $resp->renderView('impressum');
    }

    /**
     * Endpoint for /index/privacy
     * @param Response $resp
     * @throws ViewNotFound
     */
    public function PrivacyAction(Response $resp): void
    {
        $resp->setHtmlTitle('Datenschutz');
        $resp->renderView('privacy');
    }

    /**
     * Endpoint for /index/documentation
     * @param Response $resp
     * @throws ViewNotFound
     */
    public function DocumentationAction(Response $resp): void
    {
        $resp->setHtmlTitle('Dokumentation');
        $resp->renderView('documentation');
    }
}