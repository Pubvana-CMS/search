<?php

declare(strict_types=1);

namespace Pubvana\Search\Controllers;

use Pubvana\Admin\Controllers\PublicController;

/**
 * Public-facing search controller - handles /search?q=term requests.
 */
class SearchPublicController extends PublicController
{
    /**
     * Display search results.
     */
    public function search(): void
    {
        $query = trim((string) ($this->app->request()->query->q ?? ''));
        $page = max(1, (int) ($this->app->request()->query->page ?? 1));

        $data = [
            'title'   => 'Search',
            'query'   => $query,
            'results' => [],
            'total'   => 0,
            'error'   => null,
        ];

        if ($query !== '') {
            $result = $this->app->search()->search($query, $page);

            $data['results']    = $result['items'];
            $data['total']      = $result['total'];
            $data['error']      = $result['error'];
            $data['pagination'] = $this->buildPagination($result);
        }

        $this->render('search', $data);
    }

    /**
     * Build pagination data from search results.
     */
    private function buildPagination(array $result): ?array
    {
        $page = (int) ($result['page'] ?? 1);
        $perPage = (int) ($result['per_page'] ?? 10);
        $total = (int) ($result['total'] ?? 0);
        $pages = (int) ceil($total / max($perPage, 1));

        if ($pages <= 1) {
            return null;
        }

        $query = urlencode((string) ($result['query'] ?? ''));

        return [
            'current' => $page,
            'total'   => $pages,
            'prev'    => $page > 1 ? '/search?q=' . $query . '&page=' . ($page - 1) : null,
            'next'    => $page < $pages ? '/search?q=' . $query . '&page=' . ($page + 1) : null,
        ];
    }
}
