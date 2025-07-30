<?php

namespace Vendimia\Http\BodyParser;

use Vendimia\Http\Request;
use Vendimia\Exception\VendimiaException;
use Exception;
use JsonException;

/**
 * Parse a multipart/form-data body to obtain the parsedBody
 *
 * @author Oliver Etchebarne <yo@drmad.org>
 */
class FormData implements BodyParserInterface
{
    /**
     * FIXME: This should not use $_POST
     */
    public static function parseBody(Request $request): Request
    {
        $request = $request->withParsedBody($_POST);

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
