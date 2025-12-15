<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$publicPath = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($publicPath) && is_file($publicPath)) {
    $ext = pathinfo($publicPath, PATHINFO_EXTENSION);
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
        'json' => 'application/json',
        'html' => 'text/html',
        'txt' => 'text/plain'
    ];
    $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
    header('Content-Type: ' . $mimeType);
    header('Cache-Control: no-cache');
    readfile($publicPath);
    exit;
}

if (preg_match('#^/api/(.+)$#', $uri, $matches)) {
    $apiPath = __DIR__ . '/api/' . $matches[1];
    if (file_exists($apiPath)) {
        require $apiPath;
        exit;
    }
}

if (preg_match('#^/pages/(.+)$#', $uri, $matches)) {
    $pagePath = __DIR__ . '/pages/' . $matches[1];
    if (file_exists($pagePath)) {
        require $pagePath;
        exit;
    }
}

$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/public/index.php';
require __DIR__ . '/public/index.php';
