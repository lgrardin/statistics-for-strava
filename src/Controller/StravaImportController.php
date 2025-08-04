<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Process;

class StravaImportController extends AbstractController
{
    #[Route('/cron/import', name: 'cron_import', methods: ['POST'])]
    public function import(Request $request): JsonResponse
    {
        $token = $request->query->get('token');
        // Accept CRON_TOKEN from $_ENV, $_SERVER, or getenv()
        $expected = $_ENV['CRON_TOKEN'] ?? $_SERVER['CRON_TOKEN'] ?? getenv('CRON_TOKEN') ?: '';

        if ($token !== $expected) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $process = new Process(['php', 'bin/console', 'app:strava:import-data']);
        $process->run();

        if (!$process->isSuccessful()) {
            return $this->json(['error' => $process->getErrorOutput()], 500);
        }

        return $this->json(['status' => 'ok', 'output' => $process->getOutput()]);
    }

    #[Route('/cron/build', name: 'cron_build', methods: ['POST'])]
    public function build(Request $request): JsonResponse
    {
        $token = $request->query->get('token');
        // Accept CRON_TOKEN from $_ENV, $_SERVER, or getenv()
        $expected = $_ENV['CRON_TOKEN'] ?? $_SERVER['CRON_TOKEN'] ?? getenv('CRON_TOKEN') ?: '';

        if ($token !== $expected) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $process = new Process(['php', 'bin/console', 'app:strava:build-files']);
        $process->run();

        if (!$process->isSuccessful()) {
            return $this->json(['error' => $process->getErrorOutput()], 500);
        }

        return $this->json(['status' => 'ok', 'output' => $process->getOutput()]);
    }
}
