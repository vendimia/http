<?php

namespace Vendimia\Http\BodyParser;

use Vendimia\Http\Request;

/**
 * Parses a url-encoded body to an array.
 *
 * @author Oliver Etchebarne <yo@drmad.org>
 */
class Url implements BodyParserInterface
{
    public static function parseBody(Request $request): Request
    {
        parse_str($request->getBody()->getContents(), $parsed_body);
        $request = $request->withParsedBody($parsed_body);

        return $request;
    }
}