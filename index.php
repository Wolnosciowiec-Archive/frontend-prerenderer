<?php declare(strict_types=1);

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

$client = \JonnyW\PhantomJs\Client::getInstance();
$originalRequest = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

$factory = new BrowserRequestFactory($client, $originalRequest);
$controller = new RenderController($factory, $client, false);

print($controller->renderAction());
