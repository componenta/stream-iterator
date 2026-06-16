<?php

declare(strict_types=1);

namespace Componenta\Stdlib\Tests;

use Componenta\Stdlib\StreamIterator;
use Nyholm\Psr7\Stream;

it('iterates stream chunks without consuming data on repeated current calls', function (): void {
    $iterator = new StreamIterator(Stream::create('abcdef'), 2);

    $iterator->rewind();

    expect($iterator->current())->toBe('ab')
        ->and($iterator->current())->toBe('ab');

    $iterator->next();

    expect($iterator->current())->toBe('cd');
});

it('yields every chunk exactly once during foreach iteration', function (): void {
    $iterator = new StreamIterator(Stream::create('abcdef'), 2);

    expect(iterator_to_array($iterator, preserve_keys: false))
        ->toBe(['ab', 'cd', 'ef']);
});

it('rejects non-positive chunk sizes', function (): void {
    expect(fn () => new StreamIterator(Stream::create('abc'), 0))
        ->toThrow(\RuntimeException::class);
});
