<?php
namespace Vendimia\Http\BodyParser;

/**
 * Parses a url-encoded body to an array.
 *
 * @author Oliver Etchebarne <yo@drmad.org>
 */
class Url implements BodyParserInterface
{
    public static function canDecode(string $mime): bool
    {
        return $mime == 'application/x-www-form-urlencoded';
    }

    public static function parse($source): array
    {
        parse_str($source, $parsed_body);
        return $parsed_body;
    }
}