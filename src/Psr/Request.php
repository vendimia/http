<?php
namespace Vendimia\Http\Psr;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use InvalidArgumentException;

/** 
 * Vendimia PSR-7 RequestInterface implementation
 * 
 * @author Oliver Etchebarne <yo@drmad.org>
 */
class Request extends Message implements RequestInterface
{
    private $method;
    private $request_target = '/';
    private UriInterface $uri;

    public function getRequestTarget(): string
    {
        return $this->request_target;
    }

    public function withRequestTarget($requestTarget): self
    {
        $request = clone $this;
        $request->request_target = $requestTarget;

        return $request;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod($method): self
    {
        if (!in_array(strtolower($method), [
            'get', 'head', 'post', 'put', 'delete',
            'connect', 'options', 'trace','patch'])) {

            throw new InvalidArgumentException("Invalid HTTP method: $method");
        }

        $request = clone $this;
        $request->method = $method;

        return $request;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false): self
    {
        $message = clone $this;

        $message->uri = $uri;

        if ($uri->getHost() &&
            (!$preserveHost || !$message->hasHeader('host'))
            ) {

            $message = $message->withHeader('host', $uri->getHost());
        }

        return $message;
    }
}