<?php
namespace Vendimia\Http;

use Vendimia\Collection\Collection;

/**
 * HTTP Request from the client
 *
 * @author Oliver Etchebarne <yo@drmad.org>
 */
class Request extends Psr\ServerRequest
{
    /**
     * Default Body parsers
     */
    const REGISTERED_PARSERS = [
        BodyParser\Url::class,
        BodyParser\Json::class,
    ];

    // Shortcut to $this->getParsedBody(), in a Vendimia\Collection\Collection
    // if available
    public $parsed_body;

    // Shortcut to $this->getQueryParams(), in a Vendimia\Collection\Collection
    // if available
    public $query_params;

    /**
     * Returns a new ServerRequest object with information gathered by
     * PHP
     */
    public static function fromPHP(): self
    {
        $parsed_url = parse_url($_SERVER['REQUEST_URI']);

        $uri = (new Psr\Uri())
            ->withScheme($_SERVER['REQUEST_SCHEME'] ??
                (($_SERVER['HTTPS'] ?? false) ? 'https' : 'http')
            )
            ->withHost($_SERVER['HTTP_HOST'])
            ->withPath(urldecode($parsed_url['path']))
            ->withQuery(urldecode($parsed_url['query'] ?? ''))
        ;

        $body = new Psr\Stream('php://input');

        $server_request = (new self)
            ->withMethod($_SERVER['REQUEST_METHOD'])
            ->withUri($uri)
            ->withRequestTarget($_SERVER['REQUEST_URI'])
            ->withQueryParams($_GET)
            ->withBody($body)
            ->setHeadersFromPHP()
        ;

        // Si hay contenido, intentamos parsearlo
        if ($content_type = $server_request->getHeaderLine('content-type')) {
            foreach (self::REGISTERED_PARSERS as $parse_class) {
                if ($parse_class::canDecode($content_type)) {
                    $body_content = $body->getContents();

                    // Si no hay contenido, no hacemos nada
                    if (!$body_content) {
                        break;
                    }

                    $server_request = $server_request->withParsedBody(
                        $parse_class::parse($body_content)
                    );
                    break;
                }
            }
        }

        $server_request->parsed_body = $server_request->getParsedBody();
        $server_request->query_params = $server_request->getQueryParams();

        if (class_exists(Collection::class)) {
            $server_request->parsed_body = new Collection($server_request->parsed_body);
            $server_request->query_params = new Collection($server_request->query_params);
        }

        return $server_request;
    }

    /**
     * Returns true if the method is GET
     */
    public function isGet(): bool
    {
        return strtolower($this->getMethod()) == 'get';
    }

    /**
     * Returns true if the method is POST
     */
    public function isPost(): bool
    {
        return strtolower($this->getMethod()) == 'post';
    }

    /**
     * Returns true if the method is PUT
     */
    public function isPut(): bool
    {
        return strtolower($this->getMethod()) == 'put';
    }

    /**
     * Returns true if the method is DELETE
     */
    public function isDelete(): bool
    {
        return strtolower($this->getMethod()) == 'delete';
    }

    /**
     * Returns true if the method is PATCH
     */
    public function isPatch(): bool
    {
        return strtolower($this->getMethod()) == 'patch';
    }

}
