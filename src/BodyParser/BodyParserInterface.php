<?php

namespace Vendimia\Http\BodyParser;

use Vendimia\Http\Request;

/**
 * Interface for body parsers
 *
 * @author Oliver Etchebarne <yo@drmad.org>
 */
interface BodyParserInterface
{
    /**
     * Decodes a body string into an array
     */
    public static function parseBody(Request $request): Request;
}