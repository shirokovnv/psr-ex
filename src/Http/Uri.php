<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Http;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    private const SCHEMES = ['http' => 80, 'https' => 443];

    private const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~';

    private const CHAR_SUB_DELIMITERS = '!\$&\'\(\)\*\+,;=';

    private const CHAR_GEN_DELIMITERS = ':\/\?#\[\]@';

    /**
     * @var string
     */
    private string $scheme = '';

    /**
     * @var string
     */
    private string $userInfo = '';

    /**
     * @var string
     */
    private string $host = '';

    /**
     * @var int|null
     */
    private ?int $port = null;

    /**
     * @var string
     */
    private string $path = '';

    /**
     * @var string
     */
    private string $query = '';

    /**
     * @var string
     */
    private string $fragment = '';

    /**
     * @param string $uri
     */
    public function __construct(string $uri = '')
    {
        if ($uri !== '') {
            if (($parts = parse_url($uri)) === false) {
                throw new \InvalidArgumentException(\sprintf('Unable to parse URI: "%s"', $uri));
            }

            // Apply parse_url parts to a URI.
            $this->scheme = isset($parts['scheme']) ? \strtolower($parts['scheme']) : '';
            $this->userInfo = $parts['user'] ?? '';
            $this->host = isset($parts['host']) ? \strtolower($parts['host']) : '';
            $this->port = isset($parts['port']) ? $this->filterPort($parts['port']) : null;
            $this->path = isset($parts['path']) ? $this->filterPath($parts['path']) : '';
            $this->query = isset($parts['query']) ? $this->filterQueryAndFragment($parts['query']) : '';
            $this->fragment = isset($parts['fragment']) ? $this->filterQueryAndFragment($parts['fragment']) : '';
            if (isset($parts['pass'])) {
                $this->userInfo .= ':' . $parts['pass'];
            }
        }
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getAuthority(): string
    {
        if ($this->host === '') {
            return '';
        }

        $authority = $this->host;
        if ('' !== $this->userInfo) {
            $authority = $this->userInfo . '@' . $authority;
        }

        if ($this->port !== null) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    /**
     * @return string
     */
    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * @param string $scheme
     * @return UriInterface
     */
    public function withScheme(string $scheme): UriInterface
    {
        $scheme = strtolower($scheme);

        if ($this->scheme === $scheme) {
            return $this;
        }

        $new = clone $this;
        $new->scheme = $scheme;
        $new->port = $new->filterPort($new->port);

        return $new;
    }

    /**
     * @param string $user
     * @param string|null $password
     * @return UriInterface
     */
    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        $info = \preg_replace_callback(
            '/[' . self::CHAR_GEN_DELIMITERS . self::CHAR_SUB_DELIMITERS . ']++/',
            fn (array $match) => rawurlencode($match[0]),
            $user
        );

        if ($password !== null && $password !== '') {
            $info .= ':' .
                \preg_replace_callback(
                    '/[' . self::CHAR_GEN_DELIMITERS . self::CHAR_SUB_DELIMITERS . ']++/',
                    fn (array $match) => rawurlencode($match[0]),
                    $password
                );
        }

        if ($this->userInfo === $info) {
            return $this;
        }

        $new = clone $this;
        $new->userInfo = (string) $info;

        return $new;
    }

    /**
     * @param string $host
     * @return UriInterface
     */
    public function withHost(string $host): UriInterface
    {
        $host = strtolower($host);

        if ($this->host === $host) {
            return $this;
        }

        $new = clone $this;
        $new->host = $host;

        return $new;
    }

    /**
     * @param int|null $port
     * @return UriInterface
     */
    public function withPort(?int $port): UriInterface
    {
        $port = $this->filterPort($port);

        if ($this->port === $port) {
            return $this;
        }

        $new = clone $this;
        $new->port = $port;

        return $new;
    }

    /**
     * @param string $path
     * @return UriInterface
     */
    public function withPath(string $path): UriInterface
    {
        $path = $this->filterPath($path);

        if ($this->path === $path) {
            return $this;
        }

        $new = clone $this;
        $new->path = $path;

        return $new;
    }

    /**
     * @param string $query
     * @return UriInterface
     */
    public function withQuery(string $query): UriInterface
    {
        $query = $this->filterQueryAndFragment($query);

        if ($this->query === $query) {
            return $this;
        }

        $new = clone $this;
        $new->query = $query;

        return $new;
    }

    /**
     * @param string $fragment
     * @return UriInterface
     */
    public function withFragment(string $fragment): UriInterface
    {
        $fragment = $this->filterQueryAndFragment($fragment);

        if ($this->fragment === $fragment) {
            return $this;
        }

        $new = clone $this;
        $new->fragment = $fragment;

        return $new;
    }

    public function __toString(): string
    {
        $uri = $path = '';
        $authority = $this->getAuthority();

        // weak type checks to also accept null until we can add scalar type hints
        if ($this->scheme != '') {
            $uri .= $this->scheme . ':';
        }

        if ($authority != '' || $this->scheme === 'file') {
            $uri .= '//' . $authority;
        }

        if ($authority != '' && $this->path != '' && $this->path[0] != '/') {
            $path = '/' . $this->path;
        }

        $uri .= $path;

        if ($this->query != '') {
            $uri .= '?' . $this->query;
        }

        if ($this->fragment != '') {
            $uri .= '#' . $this->fragment;
        }

        return $uri;
    }

    /**
     * @param int|string|null $port
     * @return int|null
     */
    private function filterPort($port): ?int
    {
        if ($port === null) {
            return null;
        }

        $port = (int) $port;
        if ($port < 0 || $port > 0xFFFF) {
            throw new \InvalidArgumentException(\sprintf('Invalid port: %d. Must be between 0 and 65535', $port));
        }

        return $this->isNonStandardPort($this->scheme, $port) ? $port : null;
    }

    /**
     * @param string $scheme
     * @param int $port
     * @return bool
     */
    private function isNonStandardPort(string $scheme, int $port): bool
    {
        return !isset(self::SCHEMES[$scheme]) || $port !== self::SCHEMES[$scheme];
    }

    /**
     * @param string $path
     * @return string
     */
    private function filterPath(string $path): string
    {
        return (string) \preg_replace_callback(
            '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMITERS . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            fn (array $match) => rawurlencode($match[0]),
            $path
        );
    }

    /**
     * @param string $str
     * @return string
     */
    private function filterQueryAndFragment(string $str): string
    {
        return (string) \preg_replace_callback(
            '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMITERS . '%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/',
            fn (array $match) => rawurlencode($match[0]),
            $str
        );
    }
}
