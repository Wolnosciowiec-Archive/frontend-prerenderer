<?php declare(strict_types = 1);

function get_env_array(string $name, array $default) {
    return array_map(
        function (string $str) { return trim($str); },
        isset($_ENV[$name]) ? explode(',', $name) : $default
    );
}

function get_env_integer(string $name, int $default) {
    return isset($_ENV[$name]) ? (int) $_ENV[$name] : $default;
}

function get_env_boolean(string $name, bool $default) {
    return isset($_ENV[$name]) ? (bool) $_ENV[$name] : $default;
}

function get_env_string(string $name, string $default) {
    return isset($_ENV[$name]) ? (string) $_ENV[$name] : $default;
}

return [
    // PhantomJS
    'skipped_headers' => get_env_array('FP_SKIPPED_HEADERS', [
        'content-length',
        'host',
        'connection',
        'accept-encoding',
        'x-frontend-prerenderer',
        'user-agent'
    ]),
    'allowed_domains'       => get_env_array('FP_ALLOWED_DOMAINS', ['localhost']),
    'delay'                 => get_env_integer('FP_DELAY', 0),                           // Chromium (0 to wait for DOM to load, recommended), PhantomJS
    'timeout'               => get_env_integer('FP_TIMEOUT', 0),                         // PhantomJS
    'with_images'           => get_env_boolean('FP_WITH_IMAGES', false),                 // Chromium, PhantomJS
    'debug'                 => get_env_boolean('FP_DEBUG', false),                       // PhantomJS
    'renderer'              => get_env_string('FP_RENDERER', 'chromium'),                // decides which browser to use (values: chromium|phantomjs)
    'chromium_binary'       => get_env_string('FP_CHROMIUM_BINARY', 'chromium-browser'), // Chromium (examples: chromium|chrome|chromium-browser)
    'window_size'           => get_env_string('FP_WINDOW_SIZE', '1920x1080'),            // Chromium
    'open_process_limit'    => get_env_integer('FP_OPEN_PROCESS_LIMIT', 3),              // Chromium
    'wait_for_process_time' => get_env_integer('FP_WAIT_FOR_PROCESS_TIME', 4)            // Chromium
];
