<?php
namespace Vendimia\Http\Psr;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use InvalidArgumentException;

/**
 * Vendimia PSR-7 message implementation
 */
class Message implements MessageInterface
{
    protected string $protocol_version = '1.1';
    protected StreamInterface $body;

    /**
     * Message header.
     *
     * The key is saved with its original case.
     */
    protected array $headers = [];

    /**
     * Lowercase version of the header key name.
     */
    protected array $header_case_map = [];

    /**
     * Returns the real-case header name
     */
    protected function getRealHeaderName($name): ?string
    {
        return $this->header_case_map[strtolower($name)] ?? null;
    }

    /**
     * @inherit
     */
    public function getProtocolVersion(): string
    {
        return $this->protocol_version;
    }

    /**
     * @inherit
     */
    public function withProtocolVersion($version): static
    {
        $message = clone $this;
        $message->protocol_version = $version;
        return $message;
    }

    /**
     * @inherit
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inherit
     */
    public function hasHeader($name)
    {
        return key_exists(strtolower($name), $this->header_case_map);
    }

    public function getHeader($name)
    {
        if ($this->hasHeader($name)) {
            return $this->headers[$this->header_case_map[strtolower($name)]];
        }

        return [];
    }

    /**
     * @inherit
     */
    public function getHeaderLine($name)
    {
        $header_name = $this->getRealHeaderName($name);

        if (!$header_name) {
            return '';
        }

        return join(',', $this->headers[$header_name]);
    }

    /**
     * @inherit
     */
    public function withHeader($name, $value): static
    {
        $message = clone $this;

        $header_name = $this->getRealHeaderName($name);

        if (!$header_name) {
            $header_name = $name;
            $message->header_case_map[strtolower($name)] = $name;
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $message->headers[$header_name] = $value;

        return $message;
    }

    /**
     * @inherit
     */
    public function withAddedHeader($name, $value): static
    {
        $message = clone $this;

        $header_name = $this->getRealHeaderName($name);

        if (!$header_name) {
            throw new InvalidArgumentException("Invalid header name '{$name}'");
        }

        $message->headers[$header_name][] = $value;

        return $message;

    }

    /**
     * @inherit
     */
    public function withoutHeader($name): static
    {
        $message = clone $this;

        $header_name = $this->getRealHeaderName($name);

        if ($header_name) {
            unset($message->headers[$header_name]);
            unset($message->header_case_map[strtolower($header_name)]);
        }

        return $message;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): self
    {
        $message = clone $this;
        $message->body = $body;

        return $message;
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

        return $this;
    }

}
