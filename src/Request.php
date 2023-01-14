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
    private static $REGISTERED_PARSERS = [
        'application/x-www-form-urlencoded' => BodyParser\Url::class,
        'application/json' => BodyParser\Json::class,
        'multipart/form-data' => BodyParser\FormData::class,
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
        // Evitamos que falle una URL '//' trimeando los slashes
        $parsed_url = parse_url(ltrim($_SERVER['REQUEST_URI'], '/'));

        // Si $host trae puerto, lo ignoramos
        $host = $_SERVER['HTTP_HOST'];
        if (($colon_post = strpos($host, ':')) !== false) {
            $host = substr($host, 0, $colon_post);
        }

        $uri = (new Psr\Uri)
            ->withScheme($_SERVER['REQUEST_SCHEME'] ??
                (($_SERVER['HTTPS'] ?? false) ? 'https' : 'http')
            )
            ->withHost($host)
            ->withPort($_SERVER['SERVER_PORT'])
            ->withPath($parsed_url['path'] ?? '')
            ->withQuery($parsed_url['query'] ?? '')
            ->withFragment($parsed_url['fragment'] ?? '')
        ;

        $body = new Psr\Stream('php://input');

        $server_request = (new self)
            ->withMethod($_SERVER['REQUEST_METHOD'])
            ->withUri($uri)
            ->withRequestTarget($_SERVER['REQUEST_URI'])
            ->withQueryParams($_GET)
            ->withCookieParams($_COOKIE)
            ->withBody($body)
            ->setHeadersFromPHP()
        ;

        // Si hay contenido, intentamos parsearlo
        if ($content_type = $server_request->getHeaderLine('content-type')) {
            // Ignoramos los parámetros extras
            $content_type = trim(explode(';', $content_type)[0]);

            if ($parser_class = self::$REGISTERED_PARSERS[$content_type] ?? null) {
                $server_request = $parser_class::parseBody($server_request);
            }

            /*foreach (self::$REGISTERED_PARSERS as $mime => $parse_class) {
                if ($parse_class::canDecode($content_type)) {
                    $body_content = $body->getContents();

                    // Solo parseamos si hay contenido en el body
                    if ($body_content) {
                        $server_request = $server_request->withParsedBody(
                            $parse_class::parse($body_content)
                        );
                    }

                    break;
                }
            }*/
        }

        $server_request->parsed_body = $server_request->getParsedBody();
        $server_request->query_params = $server_request->getQueryParams();

        if (class_exists(Collection::class)) {
            $server_request->parsed_body = new Collection(...$server_request->parsed_body);
            $server_request->query_params = new Collection(...$server_request->query_params);
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
