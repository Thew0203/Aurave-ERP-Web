<?php
return [
    'name'       => getenv('APP_NAME') ?: 'Aruave',
    'tagline'    => getenv('APP_TAGLINE') ?: 'Electronics & IT Industry ERP – Arduino, Networking, Computers & More',
    'env'        => getenv('APP_ENV') ?: 'production',
    'debug'      => filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN) ?: false,
    'url'        => rtrim(getenv('APP_URL') ?: 'http://localhost/Aurave/public', '/'),
    'timezone'   => getenv('DEFAULT_TIMEZONE') ?: 'UTC',
    'session_lifetime' => (int)(getenv('SESSION_LIFETIME') ?: 7200),
];
