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
    private array $server_params = [];
    private array $cookies = [];
    private array $query = [];
    private array $attributes = [];
    private array $parsed_body = [];

    public function getServerParams(): array
    {
        return $this->server_params;
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
        return $this->attributes[$name] ?? $default;
    }

    public function withAttribute($name, $value): self
    {
        $server_request = clone $this;
        $this->attributes[$name] = $value;
        return $this;
    }

    public function withoutAttribute($name): self
    {
        $server_request = clone $this;

        return $this;
    }

    /**
     * Sets all the headers from getallheaders() function, fast.
     */
    public function setHeadersFromPHP(): self
    {
        foreach (getallheaders() as $name => $value) {
            $lc_name = strtolower($name);
            $this->headers[$name][] = $value;
            $this->header_case_map[$lc_name] = $name;
        }

        // TODO: ¿Hay alguna forma de obtener el environment del servidor web
        // sin usar $_SERVER? getenv() devuelve el environment del sistema
        // operativo, donde no está p.e. REMOTE_ADDR
        $this->server_params = $_SERVER;

        return $this;
    }

}
