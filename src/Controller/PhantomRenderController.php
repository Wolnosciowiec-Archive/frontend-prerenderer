<?php declare(strict_types=1);

namespace App\Controller;

use App\Factory\PhantomRequestFactory;
use JonnyW\PhantomJs\Client;
use JonnyW\PhantomJs\Http\Request;
use JonnyW\PhantomJs\Http\Response;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Renders the page using PhantomJS
 */
class PhantomRenderController implements RenderInterface
{
    /**
     * @var PhantomRequestFactory $requestFactory
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
     * @var int $delay
     */
    private $delay;

    /**
     * @var int $timeout
     */
    private $timeout;

    /**
     * @var bool $debug
     */
    private $debug;

    public function __construct(
        PhantomRequestFactory $requestFactory,
        Client $client,
        bool $withImages = false,
        int $delay = 0,
        int $timeout = 0,
        bool $debug)
    {
        $this->requestFactory = $requestFactory;
        $this->client         = $client;
        $this->withImages     = $withImages;
        $this->delay          = $delay;
        $this->timeout        = $timeout;
        $this->debug          = $debug;
    }

    /**
     * @inheritdoc
     */
    public function renderAction(SymfonyRequest $request): SymfonyResponse
    {
        $this->client->getEngine()->addOption('--load-images=' . ($this->withImages ? 'true' : 'false') . '');
        $this->client->getEngine()->addOption('--ignore-ssl-errors=true');
        $this->client->getEngine()->setPath('./node_modules/.bin/phantomjs');
        $this->client->getEngine()->debug($this->debug);

        /**
         * @var Request $request
         **/
        $browserRequest = $this->requestFactory->createBrowserRequest($request);

        if ($this->delay > 0) {
            $browserRequest->setDelay($this->delay);
        }

        if ($this->timeout > 0) {
            $browserRequest->setTimeout($this->timeout);
            $this->client->isLazy();
        }

        /**
         * @var Response $response
         **/
        $response = $this->client->getMessageFactory()->createResponse();

        // send the request
        $response = $this->client->send($browserRequest, $response);

        if ($this->debug) {
            var_dump($this->client->getEngine()->getLog());
            var_dump($response->getConsole());
            var_dump($response->getUrl());
            exit;
        }

        return $this->convertResponse($response);
    }

    /**
     * @inheritdoc
     */
    public function canRender(string $renderName): bool
    {
        return $renderName === 'phantomjs';
    }

    private function convertResponse(Response $response): SymfonyResponse
    {
        return new SymfonyResponse(
            $response->getContent(),
            $response->getStatus(),
            $response->getHeaders()
        );
    }
}
