<?php
namespace Vendimia\Http;

use Vendimia\Collection\Collection;
use Vendimia\Http\BodyParser\BodyParserInterface;

use LogicException;

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
        'application/x-www-form-urlencoded' => BodyParser\UrlEncoded::class,
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
     * Returns a BodyParserInterface instance for the given MIME type.
     *
     * $mime can have multiple MIME types. they will be sorted by the q parameter
     *
     * @param string $content_type The MIME type of the request body.
     * @return BodyParserInterface The BodyParserInterface instance.
     */
    public static function getBodyParser(string $mime): string
    {
        $tipos = [];

        // Reordenamos los mime types
        foreach (explode(',', $mime) as $tipo) {
            $tipo = trim($tipo);

            // Si no tiene peso, le asignamos 1000
            $peso = 1000;

            $valor_q = strpos($tipo, ';q=');
            if ($valor_q !== false) {
                $peso = floatval(substr($tipo, $valor_q + 2)) * 1000;
                $tipo = substr($tipo, 0, $valor_q);
            }

            // Cualquier otra opción que no sea ;q=, la ignoramos;
            $tipo = explode(';', $tipo)[0];

            $tipos[$peso] = $tipo;
        }

        // Los de mayor valor van primero
        krsort($tipos);

        foreach ($tipos as $tipo) {
            if ($parser_class = self::$REGISTERED_PARSERS[$tipo] ?? null) {
                return $parser_class;
            }
        }

        // Si llega a este punto, no hay un parser registrado para el tipo MIME
        throw new LogicException("No parser registered for MIME type: $mime");


    /**
     * Returns a new ServerRequest object with information gathered by
     * PHP
     */
    public static function fromPHP(): self
    {
        // Evitamos que falle una URL '//' trimeando los slashes
        $parsed_url = parse_url(ltrim($_SERVER['REQUEST_URI'], '/'));

        // Si $host trae puerto, lo ignoramos
        $host = $_SERVER['HTTP_HOST'] ?? '';
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
            $parser_class = self::getBodyParser($content_type);

            // Ya que los requests son inmutables, creamos uno nuevo con
            // el parsed_body colocado
            $server_request = $parser_class::parseBody($server_request);
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
