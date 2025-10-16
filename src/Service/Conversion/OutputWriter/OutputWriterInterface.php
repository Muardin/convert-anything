<?php

namespace App\Service\Conversion\OutputWriter;
interface OutputWriterInterface
{
    public function format(): OutputFormat;

    public function extension(): string;

    public function write(array $rows): string;

    /** @param iterable<array<string,mixed>> $rows */
    public function writeAsStream(iterable $rows, $outHandle): void;
}
