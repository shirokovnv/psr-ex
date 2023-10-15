<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Http\Factory;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Shirokovnv\PsrEx\Http\Request;
use Shirokovnv\PsrEx\Http\Response;
use Shirokovnv\PsrEx\Http\ServerRequest;
use Shirokovnv\PsrEx\Http\Stream;
use Shirokovnv\PsrEx\Http\UploadedFile;
use Shirokovnv\PsrEx\Http\Uri;

// TODO: Better naming ? Psr17Factory ?
class PsrHttpFactory implements
    RequestFactoryInterface,
    ResponseFactoryInterface,
    ServerRequestFactoryInterface,
    StreamFactoryInterface,
    UploadedFileFactoryInterface,
    UriFactoryInterface
{
    /**
     * @param string $method
     * @param string|UriInterface $uri
     * @return RequestInterface
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        return new Request($method, $uri);
    }

    /**
     * @param int $code
     * @param string $reasonPhrase
     * @return ResponseInterface
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new Response($code, null, [], '1.1', $reasonPhrase);
    }

    /**
     * @param string $method
     * @param string|UriInterface $uri
     * @param array<string, mixed> $serverParams
     * @return ServerRequestInterface
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return new ServerRequest(
            $method,
            $uri,
            null,
            [],
            '1.1',
            $serverParams
        );
    }

    /**
     * @param string $content
     * @return StreamInterface
     */
    public function createStream(string $content = ''): StreamInterface
    {
        return new Stream($content);
    }

    /**
     * @param string $filename
     * @param string $mode
     * @return StreamInterface
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        if ($filename === '') {
            throw new \RuntimeException('Path cannot be empty');
        }

        if (($resource = @\fopen($filename, $mode)) === false) {
            if ($mode === '' || \in_array($mode[0], ['r', 'w', 'a', 'x', 'c'], true) === false) {
                throw new \InvalidArgumentException(\sprintf('The mode "%s" is invalid.', $mode));
            }

            throw new \RuntimeException(
                \sprintf(
                    'The file "%s" cannot be opened: %s',
                    $filename,
                    \error_get_last()['message'] ?? ''
                )
            );
        }

        return new Stream($resource);
    }

    /**
     * @param resource $resource
     * @return StreamInterface
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream($resource);
    }

    /**
     * @param StreamInterface $stream
     * @param int|null $size
     * @param int $error
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     * @return UploadedFileInterface
     */
    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface {
        $size = $size ?? $stream->getSize();

        return new UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }

    /**
     * @param string $uri
     * @return UriInterface
     */
    public function createUri(string $uri = ''): UriInterface
    {
        return new Uri($uri);
    }
}
