<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../vendor/autoload.php';

final class ResponseTest extends TestCase
{
    public function testCreateEmptyResponse()
    {
        $response = new Vendimia\Http\Response;

        $this->assertEquals(
            'HTTP/1.1 200 OK',
            trim((string)$response)
        );
    }

    public function testCreateRedirectResponse()
    {
        $response = Vendimia\Http\Response::redirect('http://some.url/');

        $this->assertEquals(
            "HTTP/1.1 303 See Other\nLocation: http://some.url/",
            trim((string)$response)
        );
    }
}
