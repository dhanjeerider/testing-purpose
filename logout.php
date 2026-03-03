<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/helpers.php';
session_destroy();
redirect('index.php');
