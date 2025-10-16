<?php

namespace App\Service\Conversion\InputParser;
interface InputParserInterface
{
    public function format(): InputFormat;

    /** @return array<int,array<string,mixed>> */
    public function parse(string $path): array;

    /** @return \Traversable<int,array<string,mixed>> */
    public function parseAsStream(string $path): \Traversable;
}
