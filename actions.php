<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/helpers.php';

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = (int)$user['id'];
        $_SESSION['admin_username'] = $user['username'];
        redirect('admin.php?msg=login-success');
    }
    redirect('login.php?error=invalid-login');
}

if ($action === 'forgot_password') {
    $email = trim($_POST['email'] ?? '');
    $adminEmail = setting($pdo, 'admin_email', 'admin@example.com');
    if (strcasecmp($email, $adminEmail) === 0) {
        $newPass = substr(bin2hex(random_bytes(6)), 0, 10);
        $hash = password_hash($newPass, PASSWORD_BCRYPT);
        $pdo->prepare('UPDATE users SET password = ? WHERE username = ?')->execute([$hash, 'admin']);
        redirect('login.php?reset=' . urlencode($newPass));
    }
    redirect('login.php?error=email-not-match');
}

if ($action === 'logout') {
    session_destroy();
    redirect('index.php');
}

if ($action === 'add_watchlist') {
    $stmt = $pdo->prepare('INSERT OR IGNORE INTO watchlist (tmdb_id, media_type, title, poster_path) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        (int)($_POST['tmdb_id'] ?? 0),
        $_POST['media_type'] ?? 'movie',
        trim($_POST['title'] ?? ''),
        $_POST['poster_path'] ?? ''
    ]);
    redirect($_POST['return_url'] ?? 'watchlist.php');
}

if ($action === 'remove_watchlist') {
    $stmt = $pdo->prepare('DELETE FROM watchlist WHERE id = ?');
    $stmt->execute([(int)($_POST['id'] ?? 0)]);
    redirect('watchlist.php');
}

if ($action === 'add_comment') {
    $stmt = $pdo->prepare('INSERT INTO comments (tmdb_id, media_type, user_name, content) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        (int)($_POST['tmdb_id'] ?? 0),
        $_POST['media_type'] ?? 'movie',
        trim($_POST['user_name'] ?? 'Guest'),
        trim($_POST['content'] ?? '')
    ]);
    redirect($_POST['return_url'] ?? 'index.php');
}

if ($action === 'react') {
    $tmdbId = (int)($_POST['tmdb_id'] ?? 0);
    $mediaType = $_POST['media_type'] ?? 'movie';
    $emoji = $_POST['emoji'] ?? '👍';
    $stmt = $pdo->prepare('INSERT INTO reactions (tmdb_id, media_type, emoji, count) VALUES (?, ?, ?, 1)
        ON CONFLICT(tmdb_id, media_type, emoji) DO UPDATE SET count = count + 1');
    $stmt->execute([$tmdbId, $mediaType, $emoji]);
    redirect($_POST['return_url'] ?? 'index.php');
}

if ($action === 'submit_upi_payment') {
    $stmt = $pdo->prepare('INSERT INTO upi_payments (user_name, user_email, transaction_id, amount, status) VALUES (?, ?, ?, ?, "pending")');
    $stmt->execute([
        trim($_POST['user_name'] ?? ''),
        trim($_POST['user_email'] ?? ''),
        trim($_POST['transaction_id'] ?? ''),
        (float)($_POST['amount'] ?? 0)
    ]);
    redirect('pricing.php?msg=payment-submitted');
}

if (!isLoggedIn()) {
    redirect('login.php');
}

if ($action === 'save_setting') {
    setSetting($pdo, $_POST['key'] ?? '', $_POST['value'] ?? '');
    redirect('admin.php?tab=settings&msg=saved');
}

if ($action === 'save_many_settings') {
    $settings = [
        'site_title', 'site_description', 'telegram_link', 'google_analytics_id', 'search_console_meta',
        'admin_email', 'subscription_enabled', 'upi_id', 'upi_qr_url',
        'razorpay_key_id', 'razorpay_key_secret', 'video_ad_enabled', 'video_ad_type', 'video_ad_url', 'video_ad_skip_seconds'
    ];
    foreach ($settings as $key) {
        setSetting($pdo, $key, $_POST[$key] ?? '');
    }
    redirect('admin.php?tab=settings&msg=settings-updated');
}

if ($action === 'import_tmdb') {
    $item = [
        'tmdb_id' => (int)($_POST['tmdb_id'] ?? 0),
        'media_type' => $_POST['media_type'] ?? 'movie',
        'title' => trim($_POST['title'] ?? ''),
        'overview' => $_POST['overview'] ?? '',
        'poster_path' => $_POST['poster_path'] ?? '',
        'backdrop_path' => $_POST['backdrop_path'] ?? '',
        'release_date' => $_POST['release_date'] ?? '',
        'vote_average' => (float)($_POST['vote_average'] ?? 0)
    ];
    $sql = 'INSERT INTO movies (tmdb_id, media_type, title, overview, poster_path, backdrop_path, release_date, vote_average, is_active, updated_at)
            VALUES (:tmdb_id,:media_type,:title,:overview,:poster_path,:backdrop_path,:release_date,:vote_average,1,CURRENT_TIMESTAMP)
            ON CONFLICT(tmdb_id, media_type) DO UPDATE SET
            title = excluded.title, overview = excluded.overview, poster_path = excluded.poster_path, backdrop_path = excluded.backdrop_path,
            release_date = excluded.release_date, vote_average = excluded.vote_average, updated_at = CURRENT_TIMESTAMP';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($item);
    redirect('admin.php?tab=movies&msg=imported');
}

if ($action === 'save_movie') {
    $stmt = $pdo->prepare('INSERT INTO movies (tmdb_id, media_type, title, overview, poster_path, backdrop_path, release_date, vote_average, download_links_json, is_featured, is_active)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)');
    $stmt->execute([
        (int)($_POST['tmdb_id'] ?? 0),
        $_POST['media_type'] ?? 'movie',
        trim($_POST['title'] ?? ''),
        trim($_POST['overview'] ?? ''),
        trim($_POST['poster_path'] ?? ''),
        trim($_POST['backdrop_path'] ?? ''),
        trim($_POST['release_date'] ?? ''),
        (float)($_POST['vote_average'] ?? 0),
        trim($_POST['download_links_json'] ?? '[]'),
        isset($_POST['is_featured']) ? 1 : 0,
    ]);
    redirect('admin.php?tab=add-post&msg=movie-added');
}

if ($action === 'delete_movie') {
    $pdo->prepare('DELETE FROM movies WHERE id = ?')->execute([(int)($_POST['id'] ?? 0)]);
    redirect('admin.php?tab=movies&msg=deleted');
}

if ($action === 'save_server') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $sql = 'UPDATE custom_servers SET name=?, type=?, url=?, url_tv=?, has_ads=?, has_4k=?, is_download=?, is_active=?, description=? WHERE id=?';
        $pdo->prepare($sql)->execute([
            trim($_POST['name'] ?? ''), $_POST['type'] ?? 'stream', trim($_POST['url'] ?? ''), trim($_POST['url_tv'] ?? ''),
            isset($_POST['has_ads']) ? 1 : 0, isset($_POST['has_4k']) ? 1 : 0, isset($_POST['is_download']) ? 1 : 0,
            isset($_POST['is_active']) ? 1 : 0, trim($_POST['description'] ?? ''), $id
        ]);
    } else {
        $sql = 'INSERT INTO custom_servers (name, type, url, url_tv, has_ads, has_4k, is_download, is_active, description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $pdo->prepare($sql)->execute([
            trim($_POST['name'] ?? ''), $_POST['type'] ?? 'stream', trim($_POST['url'] ?? ''), trim($_POST['url_tv'] ?? ''),
            isset($_POST['has_ads']) ? 1 : 0, isset($_POST['has_4k']) ? 1 : 0, isset($_POST['is_download']) ? 1 : 0,
            isset($_POST['is_active']) ? 1 : 0, trim($_POST['description'] ?? '')
        ]);
    }
    redirect('admin.php?tab=servers&msg=server-saved');
}

if ($action === 'delete_server') {
    $pdo->prepare('DELETE FROM custom_servers WHERE id = ?')->execute([(int)($_POST['id'] ?? 0)]);
    redirect('admin.php?tab=servers&msg=server-deleted');
}

if ($action === 'save_widget') {
    $pdo->prepare('INSERT INTO widgets (type, title, config_json, sort_order, is_active) VALUES (?, ?, ?, ?, ?)')->execute([
        $_POST['type'] ?? 'content_row', trim($_POST['title'] ?? ''), trim($_POST['config_json'] ?? '{}'), (int)($_POST['sort_order'] ?? 0), isset($_POST['is_active']) ? 1 : 0
    ]);
    redirect('admin.php?tab=widgets&msg=widget-saved');
}

if ($action === 'delete_widget') {
    $pdo->prepare('DELETE FROM widgets WHERE id = ?')->execute([(int)($_POST['id'] ?? 0)]);
    redirect('admin.php?tab=widgets&msg=widget-deleted');
}

if ($action === 'save_page') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    if ($slug === '') $slug = slugify($title);
    $pdo->prepare('INSERT INTO pages (title, slug, content, is_active, show_in_footer, sort_order) VALUES (?, ?, ?, ?, ?, ?)')->execute([
        $title, $slug, trim($_POST['content'] ?? ''), isset($_POST['is_active']) ? 1 : 0, isset($_POST['show_in_footer']) ? 1 : 0, (int)($_POST['sort_order'] ?? 0)
    ]);
    redirect('admin.php?tab=pages&msg=page-saved');
}

if ($action === 'delete_page') {
    $pdo->prepare('DELETE FROM pages WHERE id = ?')->execute([(int)($_POST['id'] ?? 0)]);
    redirect('admin.php?tab=pages&msg=page-deleted');
}

if ($action === 'update_upi_payment_status') {
    $pdo->prepare('UPDATE upi_payments SET status = ?, admin_note = ? WHERE id = ?')->execute([
        $_POST['status'] ?? 'pending', trim($_POST['admin_note'] ?? ''), (int)($_POST['id'] ?? 0)
    ]);
    redirect('admin.php?tab=upi&msg=payment-updated');
}

if ($action === 'delete_comment') {
    $pdo->prepare('DELETE FROM comments WHERE id = ?')->execute([(int)($_POST['id'] ?? 0)]);
    redirect('admin.php?tab=movies&msg=comment-deleted');
}

redirect('admin.php');
