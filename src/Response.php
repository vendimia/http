<?php
namespace Vendimia\Http;

use Stringable;

/**
 * HTTP Response to the client
 */
class Response extends Psr\Response implements Stringable
{
    public function __construct()
    {
        $this->body = new Psr\Stream('php://temp', 'w');
    }

    public static function fromString($string)
    {
        $body = new Psr\Stream('php://temp', 'w');
        $body->write($string);

        return (new self)
            ->withBody($body)
            ->withHeader('Content-length', strlen($string))
        ;
    }

    /**
     * Creates a response for redirect to another url, using HTTP 303.
     */
    public static function redirect($url): self
    {
        return (new self)
            ->withStatus(303, 'See Other')
            ->withHeader('Location', $url)
        ;
    }

    /**
     * Builds the HTTP response as a string
     */
    public function build(): string
    {
        // Código de respuesta
        $return = join(' ', [
            'HTTP/' . $this->getProtocolVersion(),
            $this->getStatusCode(),
            $this->getReasonPhrase(),
        ]) . "\n";

        // Cabeceras
        foreach ($this->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $return .= "{$name}: {$value}\n";
            }
        }

        // Añadimos el cuerpo
        $this->getBody()->rewind();
        $body = $this->getBody()->getContents();

        if ($body) {
            $return .= "\n" . $body;
        }

        return $return;
    }

    /**
     * Stringable:__toString() implementation
     */
    public function __toString()
    {
        return $this->build();
    }

    /**
     * Creates an actual HTTP response and sends it to the client.
     */
    public function send()
    {
        // Código de respuesta
        header(join(' ', [
            'HTTP/' . $this->getProtocolVersion(),
            $this->getStatusCode(),
            $this->getReasonPhrase(),
        ]));

        // Cabeceras
        foreach ($this->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("{$name}: {$value}");
            }
        }

        // Enviamos la data
        $this->getBody()->rewind();
        echo $this->getBody()->getContents();

        // Y listo.
        exit;
    }

    /**
     * Returns a new Response with a JSON body
     */
    public static function json(array $payload)
    {
        return self::fromString(json_encode($payload))
            ->withHeader('Content-type', 'application/json')
        ;
    }
}
