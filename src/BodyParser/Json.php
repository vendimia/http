<?php
namespace Vendimia\Http\BodyParser;

use Exception;
use JsonException;
use Vendimia\Exception\VendimiaException;

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
        try {
            $result = json_decode(
                $source,
                associative: true,
                flags: JSON_THROW_ON_ERROR
            );
        } catch (JsonException $e) {
            // Lo rethrowamos
            if (class_exists(VendimiaException::class)) {
                throw new VendimiaException("Error parsing JSON body: " . $e->getMessage(),
                    original_body: $source,
                );
            } else {
                throw new Exception("Error parsing JSON body: " . $e->getMessage());
            }
        }

        return $result;
    }
}