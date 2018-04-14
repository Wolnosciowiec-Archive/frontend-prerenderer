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

use App\Controller\RenderInterface;
use App\Manager\VisitedUrlsManager;
use App\Repository\ConfigurationRepository;
use Symfony\Component\HttpFoundation\{Request, Response};

require __DIR__ . '/vendor/autoload.php';

function emitResponse (Response $response, Request $request, VisitedUrlsManager $manager = null)
{
    $headers = $response->headers->all();

    if ($manager !== null && $response->getStatusCode() >= 200 && $response->getStatusCode() < 400) {
        $manager->addUrl($request->getRequestUri());
    }

    $headers['content-length'] = strlen($response->getContent() ?? '');

    // remove unwanted headers
    unset(
        $headers['content-encoding'],
        $headers['cookie'],
        $headers['transfer-encoding']
    );

    http_response_code($response->getStatusCode());

    foreach ($headers as $headerName => $values) {
        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $value) {
            header($headerName . ': ' . $value);
        }
    }

    echo $response->getContent();
}

// bootstrap
$builder = new DI\ContainerBuilder();
require_once __DIR__ . '/src/DependencyInjection/Services.php';
$container = $builder->build();

/**
 * @var VisitedUrlsManager $manager
 * @var RenderInterface $controller
 * @var ConfigurationRepository $config
 */
$controller = $container->get(RenderInterface::class);
$manager    = $container->get(VisitedUrlsManager::class);
$config     = $container->get(ConfigurationRepository::class);

// handle the request
$request   = Request::createFromGlobals();
$customUrl = $request->headers->get('X-Render-Url');
$isForwardedRequest = false;

if ($customUrl && filter_var($customUrl, FILTER_VALIDATE_URL)) {
    $request = Request::create(
        $customUrl,
        'GET',
        [],
        $request->cookies->all(),
        $request->files->all(),
        $request->server->all()
    );

    $isForwardedRequest = true;
}

// prevalidation
if (!empty($config->get('allowed_domains')) && !in_array($request->getHttpHost(), $config->get('allowed_domains'), true)) {
    emitResponse(new Response('Domain not allowed', Response::HTTP_FORBIDDEN), $request, $manager);
    exit();
}

$response = $controller->renderAction($request);
emitResponse($response, $request, $isForwardedRequest === false ? $manager : null);
