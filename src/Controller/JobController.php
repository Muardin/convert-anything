<?php

namespace App\Controller;

use App\Entity\ConversionJob;
use App\Message\RunConversion;
use App\Service\Conversion\InputParser\InputFormat;
use App\Service\Conversion\OutputWriter\OutputFormat;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JobController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface $bus,
        private FilesystemOperator $inputStorage,
        private FilesystemOperator $outputStorage,
    )
    {
    }

    #[Route('/jobs', name: 'jobs_create', methods: ['POST'])]
    public function create(Request $req, ValidatorInterface $v): JsonResponse
    {
        /** @var UploadedFile|null $file */
        $file = $req->files->get('file');
        $output = OutputFormat::from($req->request->get('output'));

        $violations = $v->validate(
            ['file' => $file],
            new Assert\Collection([
                'file' => [new Assert\NotNull()],
            ])
        );
        if ($violations->count() > 0) {
            return $this->json(['errors' => (string)$violations], 400);
        }

        $inType = InputFormat::from(strtolower($file->getClientOriginalExtension()));

        $tmpPath = $file->getRealPath();
        $stored = 'job-' . uniqid() . '.' . $inType->value;
        $this->inputStorage->writeStream($stored, fopen($tmpPath, 'r'));
        $absPath = $this->getProjectDir() . '/var/storage/in/' . $stored;

        $job = new ConversionJob($inType->value, $output->value, $absPath);
        $this->em->persist($job);
        $this->em->flush();

        $this->bus->dispatch(new RunConversion($job->getId()));

        return $this->json(['id' => $job->getId(), 'status' => $job->getStatus()], 202);
    }

    #[Route('/jobs/{id}', name: 'jobs_status', methods: ['GET'])]
    public function status(int $id): JsonResponse
    {
        $job = $this->em->find(ConversionJob::class, $id);
        if (!$job) throw new NotFoundHttpException();

        $resp = [
            'id' => $job->getId(),
            'status' => $job->getStatus(),
            'error' => $job->getError(),
        ];
        if ($job->getStatus() === ConversionJob::STATUS_DONE) {
            $resp['downloadUrl'] = sprintf('/jobs/%d/result', $job->getId());
        }
        return $this->json($resp);
    }

    #[Route('/jobs/{id}/result', name: 'jobs_result', methods: ['GET'])]
    public function result(int $id): Response
    {
        $job = $this->em->find(ConversionJob::class, $id);
        if (!$job || $job->getStatus() !== ConversionJob::STATUS_DONE || !$job->getOutputPath()) {
            throw new NotFoundHttpException();
        }
        $path = $job->getOutputPath();
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $mime = $ext === 'json' ? 'application/json' : 'application/xml';

        $content = $this->outputStorage->read($path);
        return new Response($content, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'attachment; filename="result.' . $ext . '"',
        ]);


//        return new StreamedResponse(function () use ($path) {
////            readfile($path);
//            $stream = $this->outputStorage->readStream($path);
//            fpassthru($stream);
//            if (is_resource($stream)) {
//                fclose($stream);
//            }
//        }, 200, [
//            'Content-Type' => $ext === 'json' ? 'application/json' : 'application/xml',
//            'Content-Disposition' => 'attachment; filename="result.' . $ext . '"'
//        ]);
    }

    private function getProjectDir(): string
    {
        return dirname(__DIR__, 2);
    }
}
