<?php declare(strict_types=1);

namespace App\Controller;

use App\Factory\BrowserRequestFactory;
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
     * @param BrowserRequestFactory $requestFactory
     * @param Client $client
     * @param bool $withImages
     */
    public function __construct(BrowserRequestFactory $requestFactory, Client $client, bool $withImages = false)
    {
        $this->requestFactory = $requestFactory;
        $this->client         = $client;
        $this->withImages     = $withImages;
    }

    /**
     * @return string
     */
    public function renderAction(): string
    {
        $this->client->getEngine()->addOption('--load-images=' . ($this->withImages ? 'true' : 'false') . '');
        $this->client->getEngine()->addOption('--ignore-ssl-errors=true');
        $this->client->getEngine()->setPath('./node_modules/.bin/phantomjs');

        /**
         * @var Request $request
         **/
        $request = $this->requestFactory->createBrowserRequest();
        $request->setDelay(2);

        /**
         * @var Response $response
         **/
        $response = $this->client->getMessageFactory()->createResponse();

        // send the request
        $this->client->send($request, $response);

        return $response->getContent() ?? 'Error 503 - no response from browser';
    }
}
