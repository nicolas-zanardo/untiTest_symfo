<?php

namespace App\Service;

use App\Enum\HealthyStatus;
use http\Exception\RuntimeException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger
    ){}

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getHealthReport(string $dinosaurName): HealthyStatus {

        $health = HealthyStatus::HEALTHY;

        $response = $this->httpClient->request(
            method: 'GET',
            url: 'https://api.github.com/repos/SymfonyCasts/dino-park/issues'
        );

        $this->logger->info('Request Dino Issues',
            [
                'dino' => $dinosaurName,
                'responseStatus' => $response->getStatusCode(),
            ]
        );



        foreach ($response->toArray() as $issue) {
            if(str_contains($issue['title'], $dinosaurName)) {
                $health = $this->getDinoStatusFromLabels($issue['labels']);
            }
        }

        // Call GitHub API
        // Filter the issue
        return $health;
    }

    private function getDinoStatusFromLabels(array $labels): HealthyStatus
    {
        $health = null;
        foreach ($labels as $label) {
            $label = $label['name'];
            // We only care about "Status" labels
            if (!str_starts_with($label, 'Status:')) {
                continue;
            }
            // Remove the "Status:" and whitespace from the label
            $status = trim(substr($label, strlen('Status:')));
            $health = HealthyStatus::tryFrom($status);
            // Determine if we know about the label - throw an exception if we don't
            if (null === $health) {
                throw new \RuntimeException(sprintf('%s is an unknown status label!', $label));
            }
        }
        return $health ?? HealthyStatus::HEALTHY;
    }

}