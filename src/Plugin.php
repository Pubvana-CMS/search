<?php

declare(strict_types=1);

namespace Pubvana\Search;

use Enlivenapp\FlightSchool\PluginInterface;
use Pubvana\Search\Services\SearchService;
use flight\Engine;
use flight\net\Router;

class Plugin implements PluginInterface
{
    public function register(Engine $app, Router $router, array $config = []): void
    {
        // Core search service
        $app->map('search', function () use ($app) {
            static $instance = null;
            if ($instance === null) {
                $instance = new SearchService($app);
            }
            return $instance;
        });

        // Search form block - placeable in any theme region
        $app->adext('block', 'available', 'pubvana.search.form', [
            'label'       => 'Search Form',
            'description' => 'Site search form',
            'provider'    => fn(array $options) => [
                'action'      => $options['action'] ?? '/search',
                'label'       => $options['label'] ?? 'Search',
                'placeholder' => $options['placeholder'] ?? 'Search...',
                'button_text' => $options['button_text'] ?? 'Go',
            ],
            'template'    => 'pubvana/search/public/blocks/search',
            'priority'    => 10,
            'options'     => [
                'action'      => ['type' => 'input', 'label' => 'Form Action URL', 'default' => '/search'],
                'label'       => ['type' => 'input', 'label' => 'Label', 'default' => 'Search'],
                'placeholder' => ['type' => 'input', 'label' => 'Placeholder', 'default' => 'Search...'],
                'button_text' => ['type' => 'input', 'label' => 'Button Text', 'default' => 'Go'],
            ],
        ]);

        // Register CSS for public search results
        $app->adext('head', 'css', 'pubvana.search', [
            'priority' => 30,
            'files'    => ['css/search.css'],
            'vendor'   => 'pubvana',
            'package'  => 'search',
        ]);
    }
}
