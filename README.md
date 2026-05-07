# Pubvana Search

**I noticed folks downloading some of these packages. I'm super grateful, Thank You!  I would like to let folks know until this notice disappears I'm doing a lot of breaking changes without worrying about them.  Once versions are up around 0.5.x things should settle down.**

Full site search package for Pubvana. Searches across all registered content types with relevance-ranked results and highlighted excerpts.

## Requirements

- PHP 8.1+
- `enlivenapp/flight-school` ^0.3
- `enlivenapp/flight-settings` ^0.2

## Features

- Searches published blog posts and pages out of the box (when those packages register as providers)
- Relevance-ranked results - title matches score higher than content matches
- Multi-word queries score exact phrase matches and individual word matches
- Highlighted search terms in results using `<mark>` tags
- Paginated results
- Extensible - any package can register as a searchable content provider
- Search form block for theme region placement
- Configurable results per page and minimum query length via settings

## Public Routes

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/search?q=term` | Search results page |

## Service Usage

The search service is available as `$app->search()`:

```php
$results = $app->search()->search('my query', $page);
```

Returns:

```php
[
    'items'    => [...],   // Array of result arrays
    'total'    => 12,      // Total result count
    'page'     => 1,       // Current page
    'per_page' => 10,      // Results per page
    'query'    => 'my query',
    'error'    => null,    // Error message or null
]
```

Each result item:

```php
[
    'title'        => 'Post Title',
    'url'          => '/blog/post-slug',
    'excerpt'      => 'Matched excerpt with <mark>highlighted</mark> terms...',
    'content_type' => 'Post',
    'published_at' => '2026-05-06 12:00:00',
    'relevance'    => 16,
]
```

## Registering a Content Type as Searchable

Any package can make its content searchable by registering a search provider via adext in its `Plugin.php`:

```php
$app->adext('search', 'provider', 'yourvendor.your-package', [
    'label'    => 'Your Content',
    'callable' => function (string $term): array {
        // Query your content using ActiveRecord like()
        // Return array of result arrays with keys:
        //   title, url, excerpt, content_type, published_at, relevance
        return $results;
    },
]);
```

Relevance scoring is up to each provider. Recommended weights:
- Title match: 10
- Excerpt match: 5
- Content match: 3
- Per-word title match: 3
- Per-word excerpt match: 2
- Per-word content match: 1

## Configuration

Settings are stored in the `settings` table with class `Search`:

| Key | Default | Description |
|-----|---------|-------------|
| `results_per_page` | 10 | Number of results per page |
| `min_query_length` | 3 | Minimum characters required to search |

## Search Form Block

The package registers a `pubvana.search.form` block that can be placed in any theme region. Block options:

- **Form Action URL** - defaults to `/search`
- **Label** - defaults to `Search`
- **Placeholder** - defaults to `Search...`
- **Button Text** - defaults to `Go`

## License

MIT
