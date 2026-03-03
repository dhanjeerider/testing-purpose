<?php
function tmdbRequest(array $config, string $endpoint, array $query = []): ?array {
    if (empty($config['tmdb_api_key'])) return null;

    $query['api_key'] = $config['tmdb_api_key'];
    $url = rtrim($config['tmdb_base'], '/') . '/' . ltrim($endpoint, '/') . '?' . http_build_query($query);

    $ctx = stream_context_create([
        'http' => [
            'timeout' => 12,
            'ignore_errors' => true,
        ]
    ]);

    $resp = @file_get_contents($url, false, $ctx);
    if ($resp === false) return null;

    $json = json_decode($resp, true);
    return is_array($json) ? $json : null;
}

function fetchTrending(array $config, string $mediaType = 'all'): array {
    $data = tmdbRequest($config, "trending/$mediaType/week");
    return $data['results'] ?? [];
}

function searchTMDB(array $config, string $query, string $type = 'multi'): array {
    $endpoint = $type === 'movie' ? 'search/movie' : ($type === 'tv' ? 'search/tv' : 'search/multi');
    $data = tmdbRequest($config, $endpoint, ['query' => $query]);
    return $data['results'] ?? [];
}

function fetchDetails(array $config, string $mediaType, int $id): ?array {
    return tmdbRequest($config, "$mediaType/$id", ['append_to_response' => 'credits,videos,watch/providers,similar']);
}
