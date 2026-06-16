<?php

declare(strict_types=1);

namespace Componenta\Stdlib;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * Iterator for reading a PSR-7 stream in chunks.
 */
final class StreamIterator implements \Iterator, \Stringable
{
    private(set) int $bytesPerIteration;
    private StreamInterface $stream;
    private ?string $current = null;
    private int $key = 0;
    private bool $valid = false;

    /**
     * @param StreamInterface $stream The stream to iterate over.
     * @param int $bytesPerIteration Number of bytes to read per iteration.
     * @throws RuntimeException If the stream is not readable.
     */
    public function __construct(StreamInterface $stream, int $bytesPerIteration = 1024)
    {
        $this->assertReadable($stream);
        $this->assertPositiveBytes($bytesPerIteration);

        $this->stream            = $stream;
        $this->bytesPerIteration = $bytesPerIteration;
    }

    /**
     * Returns the entire stream content as a string.
     */
    public function __toString(): string
    {
        return (string) $this->stream;
    }

    /**
     * Creates a new instance with a different stream.
     *
     * @param StreamInterface $stream The new stream.
     * @return self
     */
    public function withStream(StreamInterface $stream): self
    {
        $this->assertReadable($stream);

        $copy = clone $this;
        $copy->stream = $stream;
        $copy->current = null;
        $copy->key = 0;
        $copy->valid = false;

        return $copy;
    }

    /**
     * Creates a new instance with different bytes per iteration.
     *
     * @param int $bytes Number of bytes.
     * @return self
     */
    public function withBytes(int $bytes): self
    {
        $this->assertPositiveBytes($bytes);

        $copy = clone $this;
        $copy->bytesPerIteration = $bytes;

        return $copy;
    }

    /**
     * Reads and returns the current chunk.
     */
    public function current(): string
    {
        return $this->current ?? '';
    }

    /**
     * No-op, reading advances the stream position automatically.
     */
    public function next(): void
    {
        $this->readNextChunk();
    }

    /**
     * Returns the current position in the stream.
     */
    public function key(): int
    {
        return $this->key;
    }

    /**
     * Checks if the stream has more data to read.
     */
    public function valid(): bool
    {
        return $this->valid;
    }

    /**
     * Rewinds the stream to the beginning.
     */
    public function rewind(): void
    {
        $this->stream->rewind();
        $this->readNextChunk();
    }

    private function readNextChunk(): void
    {
        if ($this->stream->eof()) {
            $this->current = null;
            $this->valid   = false;

            return;
        }

        $this->key = $this->stream->tell();
        $chunk = $this->stream->read($this->bytesPerIteration);

        if ($chunk === '' && $this->stream->eof()) {
            $this->current = null;
            $this->valid   = false;

            return;
        }

        $this->current = $chunk;
        $this->valid   = true;
    }

    private function assertReadable(StreamInterface $stream): void
    {
        if (!$stream->isReadable()) {
            throw new RuntimeException('Stream is not readable');
        }
    }

    private function assertPositiveBytes(int $bytes): void
    {
        if ($bytes < 1) {
            throw new RuntimeException('Bytes per iteration must be greater than zero');
        }
    }
}
