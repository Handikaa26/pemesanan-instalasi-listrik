<?php

// Teruskan semua request dari Vercel Serverless ke entry point publik Laravel / PHP
if (file_exists(__DIR__ . '/../public/index.php')) {
    require __DIR__ . '/../public/index.php';
} else {
    require __DIR__ . '/../index.php';
}
