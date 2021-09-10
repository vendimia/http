<?php
namespace Vendimia\Http\BodyParser;

/**
 * Parses a JSON body to an array.
 *
 * @author Oliver Etchebarne <yo@drmad.org>
 */
class Json implements BodyParserInterface
{
    public static function canDecode(string $mime): bool
    {
        return $mime == 'application/json';
    }

    public static function parse($source): array
    {
        return json_decode($source, associative: true);
    }
}