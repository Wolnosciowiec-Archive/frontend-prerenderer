<?php

return [
    'skipped_headers' => [                    // PhantomJS
        'content-length',
        'host',
        'connection',
        'accept-encoding',
        'x-frontend-prerenderer',
        'user-agent'
    ],
    'allowed_domains'       => ['localhost'],
    'delay'                 => 5,                    // PhantomJS
    'timeout'               => 0,                    // PhantomJS
    'with_images'           => false,                // Chromium, PhantomJS
    'debug'                 => false,                // PhantomJS
    'renderer'              => 'chromium',           // decides which browser to use (values: chromium|phantomjs)
    'chromium_binary'       => 'chromium-browser',   // Chromium (examples: chromium|chrome|chromium-browser)
    'window_size'           => '1920x1080',          // Chromium
    'open_process_limit'    => 3,                    // Chromium
    'wait_for_process_time' => 4                     // Chromium
];
