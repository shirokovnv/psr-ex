<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Http;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface, \Stringable
{
    private const SEEKABLE_BIT = 1 << 0;
    private const READABLE_BIT = 1 << 1;
    private const WRITABLE_BIT = 1 << 2;

    /**
     * @see https://www.php.net/manual/en/function.fopen.php
     * @see https://www.php.net/manual/en/function.gzopen.php
     */
    private const READABLE_MODES = '/r|a\+|ab\+|w\+|wb\+|x\+|xb\+|c\+|cb\+/';
    private const WRITABLE_MODES = '/a|w|r\+|rb\+|rw|x|c/';

    /**
     * @var resource|null
     */
    private $stream = null;

    /**
     * @var int|null
     */
    private ?int $size = null;

    /**
     * @var int
     */
    private int $metaDataBits = 0;

    /**
     * @var string|null
     */
    private ?string $uri = null;

    /**
     * @param mixed $stream
     */
    public function __construct(mixed $stream)
    {
        // TODO: move check to factory method "createStream" ?
        if (is_scalar($stream)) {
            $resource = fopen('php://temp', 'r+');
            if ($resource !== false) {
                fwrite($resource, (string) $stream);
                fseek($resource, 0);
            }

            $stream = $resource;
        }

        if (!is_resource($stream)) {
            throw new \InvalidArgumentException('Stream must be a resource');
        }

        $this->stream = $stream;
        $meta = stream_get_meta_data($this->stream);
        if ($meta['seekable']) {
            $this->metaDataBits |= self::SEEKABLE_BIT;
        }
        if ((bool) preg_match(self::READABLE_MODES, $meta['mode'])) {
            $this->metaDataBits |= self::READABLE_BIT;
        }
        if ((bool) preg_match(self::WRITABLE_MODES, $meta['mode'])) {
            $this->metaDataBits |= self::WRITABLE_BIT;
        }
        $this->uri = $meta['uri'];
    }

    /**
     * Close and detach stream on destroy.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if ($this->isSeekable()) {
            $this->rewind();
        }

        return $this->getContents();
    }

    /**
     * @return void
     */
    public function close(): void
    {
        if (isset($this->stream)) {
            if (\is_resource($this->stream)) {
                \fclose($this->stream);
            }
            $this->detach();
        }
    }

    /**
     * @return resource|null
     */
    public function detach()
    {
        if (!isset($this->stream)) {
            return null;
        }

        $result = $this->stream;
        unset($this->stream);
        $this->size = $this->uri = null;
        $this->metaDataBits = 0;

        return $result;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        if ($this->size !== null) {
            return $this->size;
        }

        if (!isset($this->stream)) {
            return null;
        }

        // Clear the stat cache if the stream has a URI
        if ($this->uri !== null) {
            \clearstatcache(true, $this->uri);
        }

        $stats = \fstat($this->stream);
        if (isset($stats['size'])) {
            $this->size = $stats['size'];

            return $this->size;
        }

        return null;
    }

    /**
     * @return int
     */
    public function tell(): int
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        $result = ftell($this->stream);

        if ($result === false) {
            throw new \RuntimeException('Unable to determine stream position');
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function eof(): bool
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        return feof($this->stream);
    }

    /**
     * @return bool
     */
    public function isSeekable(): bool
    {
        return ($this->metaDataBits & self::SEEKABLE_BIT) === self::SEEKABLE_BIT;
    }

    /**
     * @param int $offset
     * @param int $whence
     * @return void
     */
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }
        if (!$this->isSeekable()) {
            throw new \RuntimeException('Stream is not seekable');
        }
        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new \RuntimeException('Unable to seek to stream position '
                .$offset.' with whence '.var_export($whence, true));
        }
    }

    /**
     * @return void
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    /**
     * @return bool
     */
    public function isWritable(): bool
    {
        return ($this->metaDataBits & self::WRITABLE_BIT) === self::WRITABLE_BIT;
    }

    /**
     * @param string $string
     * @return int
     */
    public function write(string $string): int
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }
        if (!$this->isWritable()) {
            throw new \RuntimeException('Cannot write to a non-writable stream');
        }

        // We can't know the size after writing anything
        $this->size = null;
        $result = fwrite($this->stream, $string);

        if ($result === false) {
            throw new \RuntimeException('Unable to write to stream');
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isReadable(): bool
    {
        return ($this->metaDataBits & self::READABLE_BIT) === self::READABLE_BIT;
    }

    /**
     * @param int $length
     * @return string
     */
    public function read(int $length): string
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }
        if (!$this->isReadable()) {
            throw new \RuntimeException('Cannot read from non-readable stream');
        }
        if ($length < 0) {
            throw new \RuntimeException('Length parameter cannot be negative');
        }

        if ($length === 0) {
            return '';
        }

        try {
            $string = fread($this->stream, $length);
        } catch (\Exception $e) {
            throw new \RuntimeException('Unable to read from stream', 0, $e);
        }

        if ($string === false) {
            throw new \RuntimeException('Unable to read from stream');
        }

        return $string;
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (($contents = @\stream_get_contents($this->stream)) === false) {
            throw new \RuntimeException(
                'Unable to read stream contents: ' . (\error_get_last()['message'] ?? '')
            );
        }

        return $contents;
    }

    /**
     * @param string|null $key
     * @return array|bool|int|mixed|string|null
     */
    public function getMetadata(?string $key = null): mixed
    {
        if (!isset($this->stream)) {
            return $key === null ? [] : null;
        }

        $meta = \stream_get_meta_data($this->stream);

        return $key === null
            ? $meta
            : $meta[$key] ?? null;
    }
}
