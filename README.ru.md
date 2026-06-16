# Componenta Stream Iterator

Iterator для чтения PSR-7 streams фиксированными chunks.

Используйте его для uploads, downloads, hashing, conversion и других streaming workflows, где не нужно загружать весь body в память.

## Установка

```bash
composer require componenta/stream-iterator
```

Требует `psr/http-message`.

## Связанные пакеты

| Пакет | Зачем нужен здесь |
|---|---|
| `psr/http-message` | `StreamIterator` читает PSR-7 `StreamInterface`. |
| `psr/http-message` | Файловые ответы, загрузки и тело запроса могут отдавать PSR-7 streams. |
| `componenta/iterator` | Используйте `componenta/iterator`, если нужен повторный обход; `stream-iterator` хранит только текущий chunk. |
| `componenta/image-converter` | Потоки загрузок можно читать chunk-by-chunk перед обработкой медиа. |

## Использование

```php
use Componenta\Stdlib\StreamIterator;

$iterator = new StreamIterator($stream, bytesPerIteration: 1024);

foreach ($iterator as $offset => $chunk) {
    // $offset is the stream position where the chunk started.
}
```

## Контракт

`StreamIterator` реализует `Iterator` и `Stringable`.

Важное поведение:

- `current()` идемпотентен
- чтение происходит в `rewind()` и `next()`
- повторные вызовы `current()` не потребляют дополнительные bytes
- `key()` возвращает offset начала chunk
- `withStream()` и `withBytes()` возвращают cloned iterators
- `setBytes()` меняет bytes-per-iteration на текущем instance

## Модель Памяти

Iterator держит только текущий chunk. Он не кеширует предыдущие chunks и не делает one-shot stream replayable. Если нужна replayable iteration, используйте `componenta/iterator`.
