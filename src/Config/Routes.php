<?php

/**
 * Public search routes.
 *
 * routePrepend is empty so /search registers at root level.
 *
 * @package Pubvana\Search\Config
 */

use Pubvana\Search\Controllers\SearchPublicController;

/** @var \flight\net\Router $router */
/** @var \flight\Engine $app */
/** @var string $configPrepend */

$router->get('search', function () use ($app, $configPrepend) {
    (new SearchPublicController($app, $configPrepend))->search();
});
