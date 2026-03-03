<?php
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function setting(PDO $pdo, string $key, string $default = ''): string {
    $stmt = $pdo->prepare('SELECT setting_value FROM site_settings WHERE setting_key = ?');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? (string)$row['setting_value'] : $default;
}

function setSetting(PDO $pdo, string $key, string $value): void {
    $stmt = $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP)
        ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value, updated_at = CURRENT_TIMESTAMP');
    $stmt->execute([$key, $value]);
}

function isLoggedIn(): bool {
    return isset($_SESSION['admin_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/\s+/', '-', $text);
    return trim($text, '-') ?: 'page';
}

function posterUrl(?string $path): string {
    if (!$path) return 'https://placehold.co/342x513/1a1d2e/ffffff?text=No+Poster';
    if (str_starts_with($path, 'http')) return $path;
    return 'https://image.tmdb.org/t/p/w342' . $path;
}

function backdropUrl(?string $path): string {
    if (!$path) return 'https://placehold.co/1280x720/1a1d2e/ffffff?text=TMovie+PHP';
    if (str_starts_with($path, 'http')) return $path;
    return 'https://image.tmdb.org/t/p/w1280' . $path;
}

function serverBuildUrl(array $server, int $tmdbId, string $mediaType, int $season = 1, int $episode = 1): string {
    $template = $mediaType === 'tv' && !empty($server['url_tv']) ? $server['url_tv'] : $server['url'];
    $replace = [
        '{id}' => (string)$tmdbId,
        '{season}' => (string)$season,
        '{episode}' => (string)$episode,
    ];
    return strtr($template, $replace);
}

function currentPath(): string {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    return ltrim($path, '/');
}
