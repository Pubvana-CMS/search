{% extends 'layout' %}

{% block content %}
<div class="pv-search-page">
    <h1 class="pv-search-heading">Search</h1>

    <form action="{{ site.url }}/search" method="get" class="pv-search-form">
        <input type="search" name="q" class="pv-search-input" value="{{ query }}" placeholder="Search...">
        <button type="submit" class="pv-search-button">Search</button>
    </form>

    {% if error %}
    <p class="pv-search-error">{{ error }}</p>
    {% elseif query and results %}
    <p class="pv-search-results-summary">{{ total }} result{% if total != 1 %}s{% endif %} for &ldquo;{{ query }}&rdquo;</p>

    {% for result in results %}
    <article class="pv-search-result">
        <div class="pv-search-result-body">
            <h2 class="pv-search-result-title">
                <a href="{{ result.url }}">{! result.title !}</a>
            </h2>
            <p class="pv-search-result-meta">
                <span class="pv-search-result-type">{{ result.content_type }}</span>
                {% if result.published_at %}
                &middot; {{ result.published_at | date('F j, Y') }}
                {% endif %}
            </p>
            {% if result.excerpt %}
            <p class="pv-search-result-excerpt">{! result.excerpt !}</p>
            {% endif %}
        </div>
    </article>
    {% endfor %}

    {% if pagination %}
    {% include 'partials/pagination' %}
    {% endif %}

    {% elseif query %}
    <p class="pv-search-no-results">No results found for &ldquo;{{ query }}&rdquo;.</p>
    {% endif %}
</div>
{% endblock %}
