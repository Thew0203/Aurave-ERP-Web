<?php
return [
    'host'     => getenv('DB_HOST') ?: 'localhost',
    'name'     => getenv('DB_NAME') ?: 'aurave_erp',
    'user'     => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASS') ?: '',
    'charset'  => getenv('DB_CHARSET') ?: 'utf8mb4',
];
