<?php

namespace App\Service\Conversion\InputParser;

final class CsvInputParser implements InputParserInterface
{
    public function format(): InputFormat
    {
        return InputFormat::CSV;
    }

    public function parse(string $path): array
    {
        $separator = ',';

        $fh = fopen($path, 'r');
        $header = fgetcsv($fh, null, $separator, '"', '');
        $rows = [];
        while (($row = fgetcsv($fh, null, $separator, '"', '')) !== false) {
            $rows[] = array_combine($header, $row);
        }
        fclose($fh);
        return $rows;
    }

    public function parseAsStream(string $path): \Traversable
    {
        $f = new \SplFileObject($path, 'r');
        $f->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);
        $header = null;
        foreach ($f as $row) {
            if ($row === [null] || $row === false) continue;
            if ($header === null) {
                $header = $row;
                continue;
            }
            yield array_combine($header, $row);
        }
    }
}
