<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "conversion_job")]
class ConversionJob
{
    public const string STATUS_QUEUED = 'queued';
    public const string STATUS_RUNNING = 'running';
    public const string STATUS_DONE = 'done';
    public const string STATUS_FAILED = 'failed';

    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 16)]
    private string $status = self::STATUS_QUEUED;

    #[ORM\Column(length: 8)]
    private string $inputType;

    #[ORM\Column(length: 8)]
    private string $outputType;

    #[ORM\Column(length: 255)]
    private string $inputPath;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $outputPath = null;

    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTime $startedAt = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTime $finishedAt = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $error = null;

    public function __construct(string $inputType, string $outputType, string $inputPath)
    {
        $this->inputType = $inputType;
        $this->outputType = $outputType;
        $this->inputPath = $inputPath;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): ConversionJob
    {
        $this->id = $id;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): ConversionJob
    {
        $this->status = $status;
        return $this;
    }

    public function getInputType(): string
    {
        return $this->inputType;
    }

    public function setInputType(string $inputType): ConversionJob
    {
        $this->inputType = $inputType;
        return $this;
    }

    public function getOutputType(): string
    {
        return $this->outputType;
    }

    public function setOutputType(string $outputType): ConversionJob
    {
        $this->outputType = $outputType;
        return $this;
    }

    public function getInputPath(): string
    {
        return $this->inputPath;
    }

    public function setInputPath(string $inputPath): ConversionJob
    {
        $this->inputPath = $inputPath;
        return $this;
    }

    public function getOutputPath(): ?string
    {
        return $this->outputPath;
    }

    public function setOutputPath(?string $outputPath): ConversionJob
    {
        $this->outputPath = $outputPath;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): ConversionJob
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getStartedAt(): ?\DateTime
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTime $startedAt): ConversionJob
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getFinishedAt(): ?\DateTime
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTime $finishedAt): ConversionJob
    {
        $this->finishedAt = $finishedAt;
        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): ConversionJob
    {
        $this->error = $error;
        return $this;
    }
}
