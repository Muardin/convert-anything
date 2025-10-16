<?php
namespace App\Message;

final class RunConversion
{
    public function __construct(public int $jobId) {}
}
