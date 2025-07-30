<?php

namespace Vendimia\Http\BodyParser;

use Vendimia\Http\Request;
use Vendimia\Exception\VendimiaException;
use Exception;
use JsonException;

/**
 * Parses a JSON body to an array.
 *
 * @author Oliver Etchebarne <yo@drmad.org>
 */
class Json implements BodyParserInterface
{
    public static function parseBody(Request $request): Request
    {
        $body = $request->getBody()->getContents();
        if (!$body) {
            return $request;
        }

        try {
            $result = json_decode(
                $body,
                associative: true,
                flags: JSON_THROW_ON_ERROR
            );
        } catch (JsonException $e) {
            // Lo rethrowamos
            if (class_exists(VendimiaException::class)) {
                throw new VendimiaException("Error parsing JSON body: " . $e->getMessage(),
                    original_body: $body,
                );
            } else {
                throw new Exception("Error parsing JSON body: " . $e->getMessage());
            }
        }

        $request = $request->withParsedBody($result);

        return $request;
    }

    public static function parseBack(mixed $payload): string
    {
        return json_encode($payload);
    }
}
