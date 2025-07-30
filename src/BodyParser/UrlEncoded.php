<?php

namespace Vendimia\Http\BodyParser;

use Vendimia\Http\Request;

/**
 * Parses a url-encoded body to an array.
 *
 * @author Oliver Etchebarne <yo@drmad.org>
 */
class UrlEncoded implements BodyParserInterface
{
    public static function parseBody(Request $request): Request
    {
        $parsed_body = [];
        parse_str($request->getBody()->getContents(), $parsed_body);
        $request = $request->withParsedBody($parsed_body);

        return $request;
    }

    /**
     * Empty
     */
    public static function parseBack(mixed $payload): string
    {
        return '';
    }
}
