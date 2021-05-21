<?php
namespace Vendimia\Http\Psr;

use Psr\Http\Message\UriInterface;
use InvalidArgumentException;

class Uri implements UriInterface
{
    private array $components = [];

    /**
     * Creates a new URI from a string
     */
    public function __construct(?string $uri = null)
    {
        if ($uri) {
            $this->components = parse_url($uri);
        }
    }

    public function getScheme(): string
    {
        return strtolower($this->components['scheme']) ?? '';
    }

    public function getAuthority(): string
    {
        $authority = '';

        if ($user_info = $this->getUserInfo()) {
            $authority = $user_info . '@';
        }

        $authority .= $this->components['host'] ?? '';

        if ($this->components['port'] ?? false) {
            $authority .= ':' . $this->components['port'];
        }

        return $authority;
    }

    public function getUserInfo(): string
    {
        $user_info = '';

        if ($this->components['user'] ?? false)  {
            $user_info = $this->components['user'];
        }

        if ($this->components['pass'] ?? false)  {
            $user_info .= ':' . $this->components['pass'];
        }

        return $user_info;
    }

    public function getHost(): string
    {
        return $this->components['host'] ?? '';
    }

    public function getPort(): ?int
    {
        return $this->components['port'] ?? null;
    }

    public function getPath(): string
    {
        return $this->components['path'] ?? '';
    }

    public function getQuery(): string
    {
        return $this->components['query'] ?? '';
    }

    public function getFragment(): string
    {
        return $this->components['fragment'] ?? '';
    }

    public function withScheme($scheme): self
    {
        $uri = clone $this;
        $uri->components['scheme'] = $scheme;

        return $uri;
    }

    public function withUserInfo($user, $password = null): self
    {
        $uri = clone $this;
        $uri->components['user'] = $user;
        $uri->components['pass'] = $password;

        return $uri;
    }

    public function withHost($host): self
    {
        $uri = clone $this;
        $uri->components['host'] = $host;

        return $uri;
    }

    public function withPort($port): self
    {
        $uri = clone $this;
        if (isset($port)) {
            unset($uri->components['port']);
        } else {
            $uri->components['port'] = $port;
        }

        return $uri;
    }

    public function withPath($path): self 
    {
        $uri = clone $this;
        $uri->components['path'] = $path;

        return $uri;
    }

    public function withQuery($query): self 
    {
        $uri = clone $this;
        $uri->components['query'] = $query;

        return $uri;
    }

    public function withFragment($fragment): self 
    {
        $uri = clone $this;
        $uri->components['frament'] = $fragment;

        return $uri;
    }

    public function __toString()
    {
        return "";
    }
}
