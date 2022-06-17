<?php
namespace Vendimia\Http\Psr;

use Psr\Http\Message\UriInterface;
use InvalidArgumentException;
use Stringable;

class Uri implements UriInterface, Stringable
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

    /**
     * @var bool $ltrim_slash Left-trim any prepending slash
     */
    public function getPath($ltrim_slash = false): string
    {
        $path = $this->components['path'] ?? '';

        if ($ltrim_slash) {
            return ltrim($path, '/');
        }

        return $path;
    }

    /**
     * Returns the unencoded path
     */
    public function getDecodedPath($ltrim_slash = false): string
    {
        return rawurldecode($this->getPath($ltrim_slash));
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

        if ($port < 1 || $port > 65535) {
            throw InvalidArgumentException("Port out of range");
        }

        if (is_null($port)) {
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
        $uri->components['query'] = trim($query);

        return $uri;
    }

    public function withFragment($fragment): self
    {
        $uri = clone $this;
        $uri->components['fragment'] = trim($fragment);

        return $uri;
    }

    /**
     * Returns the URI built from the components
     */
    public function getString(): string
    {
        return
            (isset($this->components['scheme']) ? $this->components['scheme'] . '://' : '') .
            ($this->components['user'] ?? '') .
            (isset($this->components['pass']) ? ':' . $this->components['pass'] : '') .
            ((($this->components['user'] ?? false) || ($this->components['pass'] ?? false)) ? '@' : '') .
            ($this->components['host'] ?? '') .
            (isset($this->components['port']) ? ':' . $this->components['port'] : '') .
            ($this->getPath(true) ? '/' . $this->getPath(true) : '') .
            ((isset($this->components['query']) && $this->components['query']) ? '?' . $this->components['query'] : '') .
            (isset($this->components['fragment']) ? '#' . $this->components['fragment'] : '') .
            ''  // usado para mantener un '.' al final de la línea anterior
        ;
    }

    public function __toString(): string
    {
        return $this->getString();
    }
}
