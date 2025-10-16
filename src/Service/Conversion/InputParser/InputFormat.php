<?php

namespace App\Service\Conversion\InputParser;
enum InputFormat: string
{
    case CSV = 'csv';
    case JSON = 'json';
    case XLSX = 'xlsx';
    case ODS = 'ods';
}
