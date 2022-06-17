<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../vendor/autoload.php';

final class UriTest extends TestCase
{
    public function testStringToString()
    {
        $source = 'http://usr:pss@example.com:81/mypath/with-an-eñe/myfile.html?a=b&b[]=2&b[]=3#myfragment';
        $uri = new Vendimia\Http\Psr\Uri($source);

        $this->assertEquals(
            $source,
            (string)($uri)
        );
    }

    public function testPartsToString()
    {
        $source = 'http://usr:pss@example.com:81/mypath/myfile.html?a=b&b[]=2&b[]=3#myfragment';
        $uri = new Vendimia\Http\Psr\Uri();
        $uri = $uri->withScheme('http')
            ->withUserInfo('usr', 'pss')
            ->withHost('example.com')
            ->withPort(81)
            ->withPath('mypath/myfile.html')
            ->withQuery('a=b&b[]=2&b[]=3')
            ->withFragment('myfragment')
        ;

        $this->assertEquals(
            $source,
            (string)($uri)
        );
    }

}
