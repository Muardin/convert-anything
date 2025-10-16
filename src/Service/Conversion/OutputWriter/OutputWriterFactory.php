<?php

namespace App\Service\Conversion\OutputWriter;


final class OutputWriterFactory
{
    /** @var array<OutputFormat,OutputWriterInterface> */
    private array $map = [];

    /** @param iterable<OutputWriterInterface> $writers */
    public function __construct(iterable $writers)
    {
        foreach ($writers as $w) {
            $this->map[$w->format()->value] = $w;
        }
    }

    public function for(OutputFormat $type): OutputWriterInterface
    {
        return $this->map[$type->value] ?? throw new \InvalidArgumentException("Unsupported input: {$type->value}");
    }

    /** @return list<OutputFormat> */
    public function supported(): array
    {
        return array_values(array_map(
            fn(OutputWriterInterface $p) => $p->format(),
            $this->map
        ));
    }
}