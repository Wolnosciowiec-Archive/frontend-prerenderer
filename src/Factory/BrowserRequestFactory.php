<?php declare(strict_types=1);

namespace App\Factory;

use JonnyW\PhantomJs\Client;
use JonnyW\PhantomJs\Http\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Creates requests that will be telling the browser which page to open
 */
class BrowserRequestFactory
{
    /**
     * @var Client $client
     */
    private $client;

    /**
     * @var Request $originalRequest
     */
    private $originalRequest;

    /**
     * @param Client $client
     */
    public function __construct(Client $client, Request $originalRequest)
    {
        $this->client = $client;
        $this->originalRequest = $originalRequest;
    }

    /**
     * @return RequestInterface
     */
    public function createBrowserRequest(): RequestInterface
    {
        return $this->client->getMessageFactory()->createRequest(
            $this->originalRequest->getSchemeAndHttpHost() . $this->originalRequest->getRequestUri(),
            'GET'
        );
    }
}
