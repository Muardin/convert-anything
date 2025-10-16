<?php

namespace App\MessageHandler;

use App\Entity\ConversionJob;
use App\Message\RunConversion;
use App\Service\Conversion\ConverterPipeline;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RunConversionHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private ConverterPipeline $pipeline,
    )
    {
    }

    public function __invoke(RunConversion $message): void
    {
        /** @var ConversionJob $job */
        $job = $this->em->find(ConversionJob::class, $message->jobId);
        if (!$job) {
            return;
        }

        $job->setStatus(ConversionJob::STATUS_RUNNING);
        $job->setStartedAt(new \DateTime());
        $this->em->flush();

        try {
            $outPath = $this->pipeline->convert($job->getInputType(), $job->getOutputType(), $job->getInputPath());
            $job->setOutputPath($outPath);
            $job->setStatus(ConversionJob::STATUS_DONE);
            $job->setFinishedAt(new \DateTime());
        } catch (\Throwable $e) {
            $job->setStatus(ConversionJob::STATUS_FAILED);
            $job->setError($e->getMessage());
        }
        $this->em->flush();
    }
}
