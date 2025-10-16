<?php

namespace App\Service\Conversion;

use App\Service\Conversion\InputParser\InputFormat;
use App\Service\Conversion\InputParser\InputParserFactory;
use App\Service\Conversion\OutputWriter\OutputFormat;
use App\Service\Conversion\OutputWriter\OutputWriterFactory;
use League\Flysystem\FilesystemOperator;

final class ConverterPipeline
{
    public function __construct(
        private readonly InputParserFactory $inputFactory,
        private readonly OutputWriterFactory $outputFactory,
        private readonly FilesystemOperator $outputStorage
    )
    {
    }

    public function convert(string $in, string $out, string $inputPath): string
    {
        $parser = $this->inputFactory->for(InputFormat::from($in));
        $writer = $this->outputFactory->for(OutputFormat::from($out));

        $rows = $parser->parseAsStream($inputPath);

        $tmp = tmpfile();
        $writer->writeAsStream($rows, $tmp);
        $meta = stream_get_meta_data($tmp);
        $localPath = $meta['uri'];

        $filename = 'job-' . uniqid() . '.' . $writer->extension();
        $stream = fopen($localPath, 'r');
        $this->outputStorage->writeStream($filename, $stream);
        fclose($stream);
        fclose($tmp);

        return $filename;
    }
}
