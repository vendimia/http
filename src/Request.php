<?php
namespace Vendimia\Http;

/**
 * HTTP Request from the client
 *
 * @author Oliver Etchebarne <yo@drmad.org>
 */
class Request extends Psr\ServerRequest
{
    /**
     * Returns a new ServerRequest object with information gathered by
     * PHP
     */
    public static function fromPHP(): self
    {
        $parsed_url = parse_url($_SERVER['REQUEST_URI']);

        $uri = (new Psr\Uri())
            ->withScheme($_SERVER['REQUEST_SCHEME'] ?? 
                (($_SERVER['HTTPS'] ?? false) ? 'https' : 'http')
            )
            ->withHost($_SERVER['HTTP_HOST'])
            ->withPath(urldecode($parsed_url['path']))
            ->withQuery(urldecode($parsed_url['query'] ?? ''))
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

    /** 
     * Returns true if the method is GET
     */
    public function isGet(): boolean
    {
        return strtolower($this->getMethod()) == 'get';
    }

    /** 
     * Returns true if the method is POST
     */
    public function isPost(): boolean
    {
        return strtolower($this->getMethod()) == 'post';
    }

    /** 
     * Returns true if the method is PUT
     */
    public function isPut(): boolean
    {
        return strtolower($this->getMethod()) == 'put';
    }

    /** 
     * Returns true if the method is DELETE
     */
    public function isDelete(): boolean
    {
        return strtolower($this->getMethod()) == 'delete';
    }

    /** 
     * Returns true if the method is PATCH
     */
    public function isPatch(): boolean
    {
        return strtolower($this->getMethod()) == 'patch';
    }

}
