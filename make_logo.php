<?php
if (!is_dir(__DIR__ . '/public/images')) {
    mkdir(__DIR__ . '/public/images', 0777, true);
}
$base64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
file_put_contents(__DIR__ . '/public/images/logo-mandau.png', base64_decode($base64));
