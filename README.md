# Componenta Stream Iterator

Iterator for reading PSR-7 streams in fixed-size chunks.

Use it for uploads, downloads, hashing, conversion, and other streaming workflows where loading the full body into memory is unnecessary.

## Installation

```bash
composer require componenta/stream-iterator
```

Requires `psr/http-message`.

## Related Packages

| Package | Why it matters here |
|---|---|
| `psr/http-message` | `StreamIterator` reads PSR-7 `StreamInterface`. |
| `psr/http-message` | Uploads, downloads, and request bodies can expose PSR-7 streams. |
| `componenta/iterator` | Use it when replayable iteration is needed; stream iteration keeps only the current chunk. |
| `componenta/image-converter` | Upload streams can be read before media processing. |

## Usage

```php
use Componenta\Stdlib\StreamIterator;

$iterator = new StreamIterator($stream, bytesPerIteration: 1024);

foreach ($iterator as $offset => $chunk) {
    // $offset is the stream position where the chunk started.
}
```

## Contract

`StreamIterator` implements `Iterator` and `Stringable`.

Important behavior:

- `current()` is idempotent
- reading happens during `rewind()` and `next()`
- repeated calls to `current()` do not consume more bytes
- `key()` returns the chunk start offset
- `withStream()` and `withBytes()` return cloned iterators
- `setBytes()` mutates bytes-per-iteration on the current instance

## Memory Model

The iterator keeps only the current chunk. It does not cache previous chunks and does not make a one-shot stream replayable. Use `componenta/iterator` if you need replayable iteration.
