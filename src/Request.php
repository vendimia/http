<?php
namespace Vendimia\Http;

/**
 * HTTP Request from the client
 */
class Request extends Psr\ServerRequest
{
    /**
     * Returns a new ServerRequest object with information gathered by
     * PHP
     *
     * @author Oliver Etchebarne <yo@drmad.org>
     */
    public static function fromPHP(): self
    {
        $parsed_url = parse_url($_SERVER['REQUEST_URI']);

        $uri = (new Psr\Uri())
            ->withScheme($_SERVER['REQUEST_SCHEME'] ?? 
                ($_SERVER['HTTPS'] ? 'https' : 'http')
            )
            ->withHost($_SERVER['HTTP_HOST'])
            ->withPath(urldecode($parsed_url['path']))
            ->withQuery(urldecode($parsed_url['query']))
        ;

        $body = new Psr\Stream('php://input');

        $server_request = (new self)
            ->withMethod($_SERVER['REQUEST_METHOD'])
            ->withUri($uri)
            ->withRequestTarget($_SERVER['REQUEST_URI'])
            ->withQueryParams($_GET)
            ->withBody($body)
            ->setHeadersFromPHP()
        ;

        return $server_request;
    }

}
