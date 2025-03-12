<?php

declare(strict_types=1);

namespace NetBrothers\NbFeed\Adapter;

use GuzzleHttp\Client;
use Laminas\Feed\Reader\Http\ClientInterface;
use Laminas\Feed\Reader\Http\Response;
use Laminas\Feed\Reader\Http\ResponseInterface;

/**
 * NbFeed
 *
 * @author Thilo Ratnaweera <thilo.ratnaweera@netbrothers.de>
 * @copyright © 2025 NetBrothers GmbH.
 * @license MIT
 *
 * Wraps our favorite HTTP client to suffice the laminas-feed requirements.
 */
final class HttpClientAdapter implements ClientInterface
{
    private Client $wrappedClient;

    public function __construct()
    {
        $this->wrappedClient = new Client();
    }

    public function get($uri): ResponseInterface
    {
        // fetch content
        $response = $this->wrappedClient->get($uri);

        // transform headers to match requirements of ResponseInterface
        $headers = array_map(
            fn ($header) => implode(', ', $header),
            $response->getHeaders()
        );

        // return
        return new Response(
            $response->getStatusCode(),
            $response->getBody(),
            $headers
        );
    }
}
