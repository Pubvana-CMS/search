<?php

declare(strict_types=1);

namespace Pubvana\Search\Services;

use flight\Engine;

/**
 * Core search service - collects results from registered providers,
 * scores, merges, paginates, and highlights matched terms.
 */
class SearchService
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * Run a search across all registered providers.
     *
     * @param string $term  Raw search query
     * @param int    $page  Page number (1-based)
     * @return array{items: array, total: int, page: int, per_page: int, query: string, error: ?string}
     */
    public function search(string $term, int $page = 1): array
    {
        $term = trim($term);
        $perPage = (int) ($this->app->settings()->get('Search.results_per_page') ?: 10);
        $minLength = 3;

        if (mb_strlen($term) < $minLength) {
            return [
                'items'    => [],
                'total'    => 0,
                'page'     => $page,
                'per_page' => $perPage,
                'query'    => $term,
                'error'    => 'Please enter at least ' . $minLength . ' characters.',
            ];
        }

        // Collect results from all registered search providers
        $providers = $this->app->adext('search', 'provider') ?: [];
        $allResults = [];

        foreach ($providers as $provider) {
            if (isset($provider['callable']) && is_callable($provider['callable'])) {
                $results = ($provider['callable'])($term);
                if (is_array($results)) {
                    $allResults = array_merge($allResults, $results);
                }
            }
        }

        // Sort by relevance descending, then by date descending as tiebreaker
        usort($allResults, function (array $a, array $b): int {
            $rel = ($b['relevance'] ?? 0) <=> ($a['relevance'] ?? 0);
            if ($rel !== 0) {
                return $rel;
            }
            return strcmp((string) ($b['published_at'] ?? ''), (string) ($a['published_at'] ?? ''));
        });

        $total = count($allResults);
        $offset = ($page - 1) * $perPage;
        $items = array_slice($allResults, $offset, $perPage);

        // Highlight matched terms in title and excerpt
        $items = array_map(fn(array $item) => $this->highlightTerms($item, $term), $items);

        return [
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'query'    => $term,
            'error'    => null,
        ];
    }

    /**
     * Highlight matched words in title and excerpt with <mark> tags.
     *
     * HTML-escapes the text first, then injects <mark> around matches.
     */
    protected function highlightTerms(array $item, string $term): array
    {
        $words = array_unique(array_filter(preg_split('/\s+/', $term)));

        foreach (['title', 'excerpt'] as $field) {
            if (empty($item[$field])) {
                continue;
            }

            // Escape HTML first so <mark> is the only markup
            $text = htmlspecialchars((string) $item[$field], ENT_QUOTES, 'UTF-8');

            foreach ($words as $word) {
                $escaped = htmlspecialchars($word, ENT_QUOTES, 'UTF-8');
                $text = preg_replace(
                    '/(' . preg_quote($escaped, '/') . ')/iu',
                    '<mark>$1</mark>',
                    $text
                );
            }

            $item[$field] = $text;
        }

        return $item;
    }
}
