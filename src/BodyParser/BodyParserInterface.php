<?php
namespace Vendimia\Http\BodyParser;

/**
 * Interface for body parsers
 *
 * @author Oliver Etchebarne <yo@drmad.org>
 */
interface BodyParserInterface
{
    /**
     * Returns whether this parser can decode a MIME type
     */
    public static function canDecode(string $mime): bool;

    /**
     * Decodes a string into an array
     */
    public static function parse(string $source): array;
}