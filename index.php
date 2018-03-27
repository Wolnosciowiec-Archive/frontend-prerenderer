<?php declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', 'on');

/*
 * Wolnościowiec / Frontend Pre-renderer
 * -------------------------------------
 *
 *   Wolnościowiec is a project to integrate the movement
 *   of people who strive to build a society based on
 *   solidarity, freedom, equality with a respect for
 *   individual and cooperation of each other.
 *
 *   We support human rights, animal rights, feminism,
 *   anti-capitalism (taking over the production by workers),
 *   anti-racism, and internationalism. We negate
 *   the political fight and politicians at all.
 *
 *   http://wolnosciowiec.net/en
 *
 *   License: LGPLv3
 */

use App\Controller\RenderController;
use App\Factory\BrowserRequestFactory;

require __DIR__ . '/vendor/autoload.php';

// use this config as a template to create your own in the config.php, but instead of "$config =" put "return"
$config = [
    'skipped_headers' => [
        'content-length',
        'host',
        'connection',
        'accept-encoding',
        'x-frontend-prerenderer',
        'user-agent'
    ],
    'delay' => 10
];

if (is_file(__DIR__ . '/config.php')) {
    $config = array_merge($config, include __DIR__ . '/config.php');
}

$client = \JonnyW\PhantomJs\Client::getInstance();
$originalRequest = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

$manager = new \App\Manager\VisitedUrlsManager();
$factory = new BrowserRequestFactory($client, $originalRequest, $config['skipped_headers']);
$controller = new RenderController($factory, $client, false, $manager, $config['delay']);

// send response to the browser
$response = $controller->renderAction();
$headers = $response->getHeaders();

$headers['Content-Length'] = strlen($response->getContent() ?? '');

// remove unwanted headers
unset(
    $headers['Content-Encoding'],
    $headers['Cookie'],
    $headers['Transfer-Encoding']
);

http_response_code($response->getStatus());

foreach ($headers as $headerName => $value) {
    header($headerName . ': ' . $value);
}

echo $response->getContent();
