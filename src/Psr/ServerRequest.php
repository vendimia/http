<?php
namespace Vendimia\Http\Psr;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use InvalidArgumentException;

/** 
 * Vendimia PSR-7 ServerRequestInterface implementation
 * 
 * @author Oliver Etchebarne <yo@drmad.org>
 */
class ServerRequest extends Request implements ServerRequestInterface
{
    private array $params = [];
    private array $cookies = [];
    private array $query = [];
    private array $attributes = [];
    private array $parsed_body = [];

    public function getServerParams(): array
    {
        return $this->params;
    }

    public function getCookieParams(): array
    {

    }
    public function withCookieParams(array $cookies): array
    {

    }    

    public function getQueryParams(): array
    {
        return $this->query;
    }

    public function withQueryParams(array $query): self 
    {
        $server_request = clone $this;
        
        $server_request->query = $query;

        return $server_request;
    }    

    public function getUploadedFiles(): array
    {

    }

    public function withUploadedFiles(array $uploadedFiles): self
    {
        $server_request = clone $this;

        return $this;
    }    

    public function getParsedBody(): array|object|null
    {
        return $this->parsed_body;
    }

    public function withParsedBody($data): self
    {
        $server_request = clone $this;
        $server_request->parsed_body = $data;

        return $server_request;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute($name, $default = null): mixed
    {

    }

    public function withAttribute($name, $value): self
    {
        $server_request = clone $this;

        return $this;
    }

    public function withoutAttribute($name): self
    {
        $server_request = clone $this;

        return $this;
    }
}
