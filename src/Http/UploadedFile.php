<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Http;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFile implements UploadedFileInterface
{
    private const ERRORS = [
        \UPLOAD_ERR_OK,
        \UPLOAD_ERR_INI_SIZE,
        \UPLOAD_ERR_FORM_SIZE,
        \UPLOAD_ERR_PARTIAL,
        \UPLOAD_ERR_NO_FILE,
        \UPLOAD_ERR_NO_TMP_DIR,
        \UPLOAD_ERR_CANT_WRITE,
        \UPLOAD_ERR_EXTENSION,
    ];

    private const DEFAULT_BUFFER_SIZE = 1048576;

    /**
     * @var string|null
     */
    private ?string $clientFilename;

    /**
     * @var string|null
     */
    private ?string $clientMediaType;

    /**
     * @var int
     */
    private int $error;

    /**
     * @var string|null
     */
    private ?string $file;

    /**
     * @var bool
     */
    private bool $moved = false;

    /**
     * @var int|null
     */
    private ?int $size;

    /**
     * @var StreamInterface|null
     */
    private ?StreamInterface $stream;

    /**
     * @param mixed $streamOrFile
     * @param int|null $size
     * @param int $errorStatus
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     */
    public function __construct(
        mixed   $streamOrFile,
        ?int    $size,
        int     $errorStatus,
        ?string $clientFilename = null,
        ?string $clientMediaType = null
    ) {
        if (! array_key_exists($errorStatus, self::ERRORS)) {
            throw new \InvalidArgumentException('Upload file error status must be one of the "UPLOAD_ERR_*" constants');
        }

        $this->error = $errorStatus;
        $this->size = $size;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;

        if (\UPLOAD_ERR_OK === $this->error) {
            // Depending on the value set file or stream variable.
            if (\is_string($streamOrFile) && $streamOrFile !== '') {
                $this->file = $streamOrFile;
            } elseif (\is_resource($streamOrFile)) {
                $this->stream = new Stream($streamOrFile);
            } elseif ($streamOrFile instanceof StreamInterface) {
                $this->stream = $streamOrFile;
            } else {
                throw new \InvalidArgumentException('Invalid stream or file provided for UploadedFile');
            }
        }
    }

    /**
     * @return StreamInterface
     */
    public function getStream(): StreamInterface
    {
        $this->assertActive();

        if ($this->stream instanceof StreamInterface) {
            return $this->stream;
        }

        if (($resource = @\fopen((string) $this->file, 'r')) === false) {
            throw new \RuntimeException(
                \sprintf(
                    'The file "%s" cannot be opened: %s',
                    $this->file,
                    \error_get_last()['message'] ?? ''
                )
            );
        }

        return new Stream($resource);
    }

    /**
     * @param string $targetPath
     * @return void
     */
    public function moveTo(string $targetPath): void
    {
        $this->assertActive();

        if ($targetPath === '') {
            throw new \InvalidArgumentException('Invalid path provided for move operation; must be a non-empty string');
        }

        if ($this->file !== null) {
            $this->moved = 'cli' === \PHP_SAPI
                ? @\rename($this->file, $targetPath)
                : @\move_uploaded_file($this->file, $targetPath);

            if ($this->moved === false) {
                throw new \RuntimeException(
                    \sprintf(
                        'Uploaded file could not be moved to "%s": %s',
                        $targetPath,
                        \error_get_last()['message'] ?? ''
                    )
                );
            }
        } else {
            $stream = $this->getStream();
            if ($stream->isSeekable()) {
                $stream->rewind();
            }

            if (($resource = @\fopen($targetPath, 'w')) === false) {
                throw new \RuntimeException(
                    \sprintf(
                        'The file "%s" cannot be opened: %s',
                        $targetPath,
                        \error_get_last()['message'] ?? ''
                    )
                );
            }

            $dest = new Stream($resource);

            while (!$stream->eof()) {
                if (!$dest->write($stream->read(self::DEFAULT_BUFFER_SIZE))) {
                    break;
                }
            }

            $this->moved = true;
        }
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * @return string|null
     */
    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    /**
     * @return string|null
     */
    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }

    /**
     * @return void
     */
    private function assertActive(): void
    {
        if ($this->error !== \UPLOAD_ERR_OK) {
            throw new \RuntimeException('Cannot retrieve stream due to upload error');
        }

        if ($this->moved) {
            throw new \RuntimeException('Cannot retrieve stream after it has already been moved');
        }
    }
}
