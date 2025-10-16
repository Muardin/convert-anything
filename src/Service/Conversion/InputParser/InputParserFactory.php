<?php

namespace App\Service\Conversion\InputParser;

final class InputParserFactory
{
    /** @var array<InputFormat,InputParserInterface> */
    private array $map = [];

    /** @param iterable<InputParserInterface> $parsers */
    public function __construct(iterable $parsers)
    {
        foreach ($parsers as $p) {
            $this->map[$p->format()->name] = $p;
        }
    }

    public function for(InputFormat $type): InputParserInterface
    {
        return $this->map[$type->name] ?? throw new \InvalidArgumentException("Unsupported input: {$type->value}");
    }

    /** @return list<InputFormat> */
    public function supported(): array
    {
        return array_values(array_map(
            fn(InputParserInterface $p) => $p->format(),
            $this->map
        ));
    }
}
