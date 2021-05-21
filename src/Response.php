<?php
namespace Vendimia\Http;

/** 
 * HTTP Response to the client
 */
class Response extends Psr\Response
{
    public static function fromString($string)
    {
        $body = new Psr\Stream('php://temp', 'w');
        $body->write($string);
        
        return (new self)
            ->withBody($body)
        ;
    }
}