<?php
namespace Vendimia\Http\Psr;

use Psr\Http\Message\StreamInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 * Vendimia PSR-7 stream implementation
 */
class Stream implements StreamInterface
{
    private $handle;

    /**
     * Open a new stream
     */
    public function __construct($stream, $mode = 'r')
    {
        // Si $stream es un... stream, lo usamos.
        if (is_resource($stream) && get_resource_type($stream) == 'stream') {
            $this->handle = $stream;
            return;
        }

        $this->handle = fopen($stream, $mode);
    }

    public function close()
    {
        fclose($this->handle);
    }

    /**
     * @inherit
     */
    public function detach()
    {
        return $this->handle;
    }

    /**
     * @inherit
     */
    public function getSize(): ?int
    {
        return fstat($this->handle)['size'] ?? null;
    }

    /**
     * @inherit
     */
    public function tell(): int
    {
        return ftell($this->handle);
    }

    /**
     * @inherit
     */
    public function eof(): bool
    {
        return feof($this->handle);
    }

    /**
     * @inherit
     */
    public function isSeekable(): bool
    {
        return stream_get_meta_data($this->handle)['seekable'];
    }

    /**
     * @inherit
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (fseek($this->handle, $offset, $whence) != 0) {
            throw new RuntimeException("Error seeking stream");
        }
    }

    /**
     * @inherit
     */
    public function rewind()
    {
        if (rewind($this->handle) === false) {
            throw new RuntimeException("Error rewinding stream");
        }
    }

    /**
     * @inherit
     */
    public function isWritable()
    {
        $mode = stream_get_meta_data($this->handle)['mode'];
        return str_contains($mode, 'w') || str_contains($mode, 'a');
    }

    /**
     * @inherit
     */
    public function write($string): int
    {
        $count = fwrite($this->handle, $string);

        if ($count === false) {
            throw new RuntimeException('Error writing to stream.');
        }

        return $count;
    }

    /**
     * @inherit
     */
    public function isReadable(): bool
    {
        $mode = stream_get_meta_data($this->handle)['mode'];
        return str_contains($mode, 'r');
    }

    /**
     * @inherit
     */
    public function read($length): string
    {
        $data = fread($this->handle, $length);

        if ($data === false) {
            throw new RuntimeException('Error reading stream.');
        }

        return $data;
    }

    /**
     * @inherit
     */
    public function getContents(): string
    {
        $data = stream_get_contents($this->handle);

        if ($data === false) {
            throw new RuntimeException('Error reading stream.');
        }

        return $data;
    }

    /**
     * @inherit
     */
    public function getMetadata($key = null)
    {
        $metadata = stream_get_meta_data($this->resource);

        if (is_null($key)) {
            return $metadata;
        }

        return $metadata[$key] ?? null;
    }

    /**
     *
     */
    public function __toString()
    {
        $this->rewind();
        return $this->getContents();
    }
}
