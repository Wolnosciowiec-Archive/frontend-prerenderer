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
     * @var array $skippedHeaders
     */
    private $skippedHeaders;

    /**
     * @param Client $client
     * @param Request $originalRequest
     */
    public function __construct(Client $client, Request $originalRequest, array $skippedHeaders = [])
    {
        $this->client = $client;
        $this->originalRequest = $originalRequest;
        $this->skippedHeaders = $skippedHeaders;
    }

    /**
     * @return RequestInterface
     */
    public function createBrowserRequest(): RequestInterface
    {
        $browserRequest = $this->client->getMessageFactory()->createRequest(
            $this->originalRequest->getSchemeAndHttpHost() . $this->originalRequest->getRequestUri(),
            'GET'
        );

        foreach ($this->originalRequest->headers->all() as $headerName => $values) {
            if (in_array(strtolower($headerName), $this->skippedHeaders, true)) {
                continue;
            }

            foreach ($values as $value) {
                $browserRequest->addHeader($headerName, $value);
            }
        }

        $browserRequest->setRequestData($this->originalRequest->request->all());

        return $browserRequest;
    }
}
