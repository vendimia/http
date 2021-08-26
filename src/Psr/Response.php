<?php
namespace Vendimia\Http\Psr;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use InvalidArgumentException;

/** 
 * Vendimia PSR-7 ResponseInterface implementation
 * 
 * @author Oliver Etchebarne <yo@drmad.org>
 */
class Response extends Message implements ResponseInterface
{
    private $code = 200;
    private $reason = '';

    public function getStatusCode(): int
    {
        return $this->code;
    }
    
    public function withStatus($code, $reasonPhrase = ''): self
    {
        $response = clone $this;

        $response->code = $code;
        $response->reason = $reasonPhrase;

        return $response;
    }

    public function getReasonPhrase(): string
    {
        return $this->reason;
    }
}