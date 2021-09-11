<?php
namespace Vendimia\Http;

/**
 * HTTP Response to the client
 */
class Response extends Psr\Response
{
    public static function fromString($string)
    {
        $body = new Psr\Stream('php://temp', 'w');
        $body->write($string);

        return (new self)
            ->withBody($body)
        ;
    }

    /**
     * Creates a response for redirect to another url, using HTTP 303.
     */
    public static function redirect($url): self
    {
        return (new self)
            ->withStatus(303)
            ->withHeader('Location', $url)
        ;
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
}
