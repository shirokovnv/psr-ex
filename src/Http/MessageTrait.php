<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Http;

use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
    /**
     * @var array<string, string[]> Map of all registered headers, as original name => array of values
     */
    private array $headers = [];

    /**
     * @var array<string, string> Map of lowercase header name => original name at registration
     */
    private array $headerNames = [];

    /**
     * @var string
     */
    private string $protocol;

    /**
     * @var StreamInterface|null
     */
    private ?StreamInterface $stream;

    /**
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    /**
     * @param string $version
     * @return self
     */
    public function withProtocolVersion(string $version): self
    {
        if ($this->protocol === $version) {
            return $this;
        }

        $new = clone $this;
        $new->protocol = $version;

        return $new;
    }

    /**
     * @return (string|string[])[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader(string $name): bool
    {
        return isset($this->headerNames[strtolower($name)]);
    }

    /**
     * @param string $name
     * @return array<string>
     */
    public function getHeader(string $name): array
    {
        return $this->headers[$this->headerNames[strtolower($name)] ?? null] ?? [];
    }

    /**
     * @param string $name
     * @return string
     */
    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    /**
     * @param string $name
     * @param string|array<string> $value
     * @return self
     */
    public function withHeader(string $name, $value): self
    {
        $value = $this->validateAndTrimHeader($name, $value);
        $normalized = strtolower($name);

        $new = clone $this;
        if (isset($new->headerNames[$normalized])) {
            unset($new->headers[$new->headerNames[$normalized]]);
        }
        $new->headerNames[$normalized] = $name;
        $new->headers[$name] = $value;

        return $new;
    }

    /**
     * @param string $name
     * @param $value
     * @return self
     */
    public function withAddedHeader(string $name, $value): self
    {
        $new = clone $this;
        $new->setHeaders([$name => $value]);

        return $new;
    }

    /**
     * @param string $name
     * @return self
     */
    public function withoutHeader(string $name): self
    {
        $normalized = strtolower($name);
        if (!isset($this->headerNames[$normalized])) {
            return $this;
        }

        $header = $this->headerNames[$normalized];
        $new = clone $this;
        unset($new->headers[$header], $new->headerNames[$normalized]);

        return $new;
    }

    /**
     * @return StreamInterface
     */
    public function getBody(): StreamInterface
    {
        if ($this->stream === null) {
            $this->stream = new Stream(fopen('php://temp', 'r+'));
        }

        return $this->stream;
    }

    /**
     * @param StreamInterface $body
     * @return self
     */
    public function withBody(StreamInterface $body): self
    {
        if ($this->stream === $body) {
            return $this;
        }

        $new = clone $this;
        $new->stream = $body;

        return $new;
    }

    /**
     * @param (string|string[])[] $headers
     * @return void
     */
    private function setHeaders(array $headers): void
    {
        $this->headerNames = $this->headers = [];
        foreach ($headers as $header => $value) {
            if (\is_int($header)) {
                // If a header name was set to a numeric string, PHP will cast the key to an int.
                // We must cast it back to a string in order to comply with validation.
                $header = (string)$header;
            }

            $value = $this->validateAndTrimHeader($header, $value);
            $normalized = \strtolower($header);

            if (isset($this->headerNames[$normalized])) {
                $header = $this->headerNames[$normalized];
                $this->headers[$header] = \array_merge($this->headers[$header], $value);
            } else {
                $this->headerNames[$normalized] = $header;
                $this->headers[$header] = $value;
            }
        }
    }

    /**
     * Make sure the header complies with RFC 7230.
     *
     * Header names must be a non-empty string consisting of token characters.
     *
     * Header values must be strings consisting of visible characters with all optional
     * leading and trailing whitespace stripped. This method will always strip such
     * optional whitespace. Note that the method does not allow folding whitespace within
     * the values as this was deprecated for almost all instances by the RFC.
     *
     * header-field = field-name ":" OWS field-value OWS
     * field-name   = 1*( "!" / "#" / "$" / "%" / "&" / "'" / "*" / "+" / "-" / "." / "^"
     *              / "_" / "`" / "|" / "~" / %x30-39 / ( %x41-5A / %x61-7A ) )
     * OWS          = *( SP / HTAB )
     * field-value  = *( ( %x21-7E / %x80-FF ) [ 1*( SP / HTAB ) ( %x21-7E / %x80-FF ) ] )
     *
     * @see https://tools.ietf.org/html/rfc7230#section-3.2.4
     *
     * @param mixed $header
     * @param mixed $values
     * @return array<string>
     */
    private function validateAndTrimHeader(mixed $header, mixed $values): array
    {
        if (!\is_string($header) || \preg_match("@^[!#$%&'*+.^_`|~0-9A-Za-z-]+$@D", $header) !== 1) {
            throw new \InvalidArgumentException('Header name must be an RFC 7230 compatible string');
        }

        if (!\is_array($values)) {
            // This is simple, just one value.
            if ((!\is_numeric($values) && !\is_string($values)) || \preg_match("@^[ \t\x21-\x7E\x80-\xFF]*$@", (string) $values) !== 1) {
                throw new \InvalidArgumentException('Header values must be RFC 7230 compatible strings');
            }

            return [\trim((string) $values, " \t")];
        }

        if (empty($values)) {
            throw new \InvalidArgumentException('Header values must be a string or an array of strings, empty array given');
        }

        // Assert non-empty array
        $returnValues = [];
        foreach ($values as $v) {
            if ((!\is_numeric($v) && !\is_string($v)) || \preg_match("@^[ \t\x21-\x7E\x80-\xFF]*$@D", (string) $v) !== 1) {
                throw new \InvalidArgumentException('Header values must be RFC 7230 compatible strings');
            }

            $returnValues[] = \trim((string) $v, " \t");
        }

        return $returnValues;
    }
}
