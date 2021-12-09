<?php

namespace Framework\Html;

use Framework\Response;
use Framework\Session;
use Framework\Validation\Input;
use Framework\Validation\InputValidator;

abstract class Paginator
{
    public static function render(int $currentPage = 1, int $itemsPerPage = 10, int $totalItems = 0, bool $enableAjaxPagination = false): string
    {
        $filterQuery = Session::getCurrentFilterQuery();

        $pagePadding = 3;
        $totalPages = intval(ceil($totalItems / $itemsPerPage));

        $pagesBefore = '';
        if ($currentPage - $pagePadding - 1 > 0) {
            $pagesBefore .= "<a class='paginator-item no-hover text-light'>...</a>";
        }
        for ($i = $currentPage - $pagePadding; $i < $currentPage; $i++) {
            if ($i > 0) {
                $pagesBefore .= "<a href='./?page=$i&$filterQuery' class='paginator-item text-light'>$i</a>";
            }
        }

        $pagesAfter = '';
        for ($i = $currentPage + 1; $i <= min($currentPage + $pagePadding + 1, $totalPages); $i++) {
            if ($i > 0) {
                $pagesAfter .= "<a href='./?page=$i&$filterQuery' class='paginator-item text-light'>$i</a>";
            }
        }
        if ($currentPage + $pagePadding + 1 < $totalPages) {
            $pagesAfter .= "<a class='paginator-item no-hover text-light'>...</a>";
        }

        $ajaxPagination = '';
        if ($enableAjaxPagination) {
            $ajaxPagination = 'data-js="enable-ajax-pagination"';
        }

        return <<<EOF
            <div class="flexbox w12 m02 flex-center flex-top">
                <div class="paginator flexbox flex-stretch" $ajaxPagination
                    data-js-filter="$filterQuery" data-js-page="$currentPage" data-js-maxpage="$totalPages">
                   <a href="./?$filterQuery" class="paginator-item text-light"><span class="material-icons">first_page</span></a>
                   $pagesBefore
                   <a class="paginator-item text-light active">$currentPage</a>
                   $pagesAfter
                   <a href="./?page=$totalPages&$filterQuery" class="paginator-item text-light"><span class="material-icons">last_page</span></a>
                </div>
            </div>
            EOF;
    }

    /**
     * Reads the page from the GET parameters and sets all required view vars for the paginator component
     * @param Response $resp
     * @param int $itemsPerPage
     * @param int $totalItems
     * @return bool True if pagination was successfully
     */
    public static function makePagination(Response $resp, int $itemsPerPage, int $totalItems): bool
    {
        $page = self::getCurrentPage() + 1;

        $totalPages = intval(ceil($totalItems / $itemsPerPage));

        $resp->setViewVar('pagination_current_page', $page);
        $resp->setViewVar('pagination_items_per_page', $itemsPerPage);
        $resp->setViewVar('pagination_total_items', $totalItems);
        $resp->setViewVar('pagination_total_pages', $totalPages);

        if ($page === 0) {
            return true;
        }

        if ($page > intval(ceil($totalItems / $itemsPerPage)) + 1) {
            return false;
        }

        return true;
    }

    /**
     * Returns current page from GET parameter
     * @return int The current page (starting at 0)
     */
    public static function getCurrentPage(): int
    {
        $input = InputValidator::parseAndValidate([
            new Input(INPUT_GET, 'page', 'Page', false, FILTER_VALIDATE_INT),
        ]);

        if ($input->hasErrors()) {
            return 0;
        }

        $page = intval($input->getValue('page'));
        if ($page === 0) {
            return 0;
        }

        return $page - 1;
    }
}