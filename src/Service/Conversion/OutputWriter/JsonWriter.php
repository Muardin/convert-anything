<?php

namespace App\Service\Conversion\OutputWriter;

final class JsonWriter implements OutputWriterInterface
{
    public function format(): OutputFormat
    {
        return OutputFormat::JSON;
    }

    public function extension(): string
    {
        return 'json';
    }

    public function write(array $rows): string
    {
        return json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function writeAsStream(iterable $rows, $outHandle): void
    {
        fwrite($outHandle, "[");
        $first = true;
        foreach ($rows as $r) {
            if (!$first) fwrite($outHandle, ",");
            $first = false;
            fwrite($outHandle, json_encode($r, JSON_UNESCAPED_UNICODE));
        }
        fwrite($outHandle, "]");
    }
}
