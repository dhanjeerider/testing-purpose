<?php
return [
    'app_name' => getenv('APP_NAME') ?: 'TMovie PHP',
    'base_url' => getenv('BASE_URL') ?: 'http://localhost:8000',
    'tmdb_api_key' => getenv('TMDB_API_KEY') ?: '',
    'tmdb_base' => 'https://api.themoviedb.org/3',
    'db_path' => getenv('DB_PATH') ?: __DIR__ . '/database/app.db',
    'session_name' => 'tmovie_php_session',
];
