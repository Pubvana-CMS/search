<?php

return [
    'install' => [
        [
            'table' => 'settings',
            'rows'  => [
                ['class' => 'Search', 'key' => 'results_per_page', 'value' => '10', 'type' => 'integer', 'title' => 'Results Per Page', 'description' => 'Number of search results to display per page'],
            ],
        ],
    ],
];
