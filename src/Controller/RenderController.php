<?php declare(strict_types=1);

namespace App\Controller;

use App\Factory\BrowserRequestFactory;
use App\Manager\VisitedUrlsManager;
use JonnyW\PhantomJs\Client;
use JonnyW\PhantomJs\Http\Request;
use JonnyW\PhantomJs\Http\Response;

/**
 * Renders the page using PhantomJS
 */
class RenderController
{
    /**
     * @var BrowserRequestFactory $requestFactory
     */
    private $requestFactory;

    /**
     * @var Client $client
     */
    private $client;

    /**
     * @var bool $withImages
     */
    private $withImages;

    /**
     * @var VisitedUrlsManager $manager
     */
    private $manager;

    /**
     * @var int $delay
     */
    private $delay;

    public function __construct(BrowserRequestFactory $requestFactory, Client $client, bool $withImages = false, VisitedUrlsManager $manager, int $delay)
    {
        $this->requestFactory = $requestFactory;
        $this->client         = $client;
        $this->withImages     = $withImages;
        $this->manager        = $manager;
        $this->delay          = $delay;
    }

    /**
     * @return Response
     */
    public function renderAction(): Response
    {
        $this->client->getEngine()->addOption('--load-images=' . ($this->withImages ? 'true' : 'false') . '');
        $this->client->getEngine()->addOption('--ignore-ssl-errors=true');
        $this->client->getEngine()->setPath('./node_modules/.bin/phantomjs');

        /**
         * @var Request $request
         **/
        $request = $this->requestFactory->createBrowserRequest();
        $request->setDelay($this->delay);

        /**
         * @var Response $response
         **/
        $response = $this->client->getMessageFactory()->createResponse();

        // send the request
        $response = $this->client->send($request, $response);

        $statusCode = $response->getStatus();
        
        if ($statusCode >= 200 && $statusCode < 400) {
            $this->manager->addUrl($request->getUrl());
        }

        return $response;
    }
}
