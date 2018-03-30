<?php declare(strict_types=1);

namespace App\Factory;

use JonnyW\PhantomJs\Client;
use JonnyW\PhantomJs\Http\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Creates requests that will be telling the browser which page to open
 */
class PhantomRequestFactory
{
    /**
     * @var Client $client
     */
    private $client;

    /**
     * @var array $skippedHeaders
     */
    private $skippedHeaders;

    /**
     * @param Client $client
     * @param array  $skippedHeaders
     */
    public function __construct(Client $client, array $skippedHeaders = [])
    {
        $this->client = $client;
        $this->skippedHeaders = $skippedHeaders;
    }

    /**
     * @return RequestInterface
     */
    public function createBrowserRequest(Request $request): RequestInterface
    {
        $browserRequest = $this->client->getMessageFactory()->createRequest(
            $request->getSchemeAndHttpHost() . $request->getRequestUri(),
            'GET'
        );

        foreach ($request->headers->all() as $headerName => $values) {
            if (in_array(strtolower($headerName), $this->skippedHeaders, true)) {
                continue;
            }

            foreach ($values as $value) {
                $browserRequest->addHeader($headerName, $value);
            }
        }

        $browserRequest->setRequestData($request->request->all());

        return $browserRequest;
    }
}
