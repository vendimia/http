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
        // Separamos el target del query string
        $path = $_SERVER['REQUEST_URI'];
        $query_separator = strpos($path, '?');
        $query = '';

        if ($query_separator !== false) {
            $query = substr($path, $query_separator + 1);
            $path = substr($path, 0, $query_separator);
        }

        $uri = (new Psr\Uri())
            ->withScheme($_SERVER['REQUEST_SCHEME'] ?? 'http')
            ->withHost($_SERVER['HTTP_HOST'])
            ->withPath($path)
            ->withQuery($query)
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
