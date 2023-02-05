<?php
namespace App\Tests\Unit\Service;
use App\Enum\HealthyStatus;
use App\Service\GithubService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
//use Symfony\Contracts\HttpClient\HttpClientInterface;
//use Symfony\Contracts\HttpClient\ResponseInterface;
class GithubServiceTest extends TestCase
{
    private LoggerInterface $mockLogger;
    private MockHttpClient $mockHttpClient;
    private MockResponse $mockResponse;

    protected function setUp(): void
    {
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockHttpClient = new MockHttpClient();
    }

    /**
     * @dataProvider dinoNameProvider
     */
    public function testGetHealthReportReturnsCorrectHealthStatusForDino(HealthyStatus $expectedStatus, string $dinoName): void
    {
//        $mockLogger = $this->createMock(LoggerInterface::class);
//        $mockHttpClient = $this->createMock(HttpClientInterface::class);
//        $mockResponse = $this->createMock(ResponseInterface::class);
//        $mockResponse
//            ->method('toArray')
//            ->willReturn([
//                [
//                    'title' => 'Daisy',
//                    'labels' => [['name' => 'Status: Sick']],
//                ],
//                [
//                    'title' => 'Maverick',
//                    'labels' => [['name' => 'Status: Healthy']],
//                ],
//            ])
//        ;

        $service = $this->createGithubService([
            [
                'title' => 'Daisy',
                'labels' => [['name' => 'Status: Sick']],
            ],
            [
                'title' => 'Maverick',
                'labels' => [['name' => 'Status: Healthy']],
            ]
        ]);

//        $mockHttpClient
//            ->expects(self::once())
//            ->method('request')
//            ->with('GET', 'https://api.github.com/repos/SymfonyCasts/dino-park/issues')
//            ->willReturn($mockResponse)
//        ;
//        $service = new GithubService($mockHttpClient, $mockLogger);
        self::assertSame($expectedStatus, $service->getHealthReport($dinoName));
        self::assertSame(1, $this->mockHttpClient->getRequestsCount());
        self::assertSame('GET', $this->mockResponse->getRequestMethod());
        self::assertSame('https://api.github.com/repos/SymfonyCasts/dino-park/issues',
        $this->mockResponse->getRequestUrl());
    }

    public function dinoNameProvider(): \Generator
    {
        yield 'Sick Dino' => [HealthyStatus::SICK, 'Daisy',];
        yield 'Healthy Dino' => [HealthyStatus::HEALTHY, 'Maverick',];
    }

    public function testExceptionThrownWithUnknownLabel(): void
    {
        $service = $this->createGithubService([
            [
                'title' => 'Maverick',
                'labels' => [['name' => 'Status: Drowsy']],
            ],
        ]);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Drowsy is an unknown status label!');
        $service->getHealthReport('Maverick');
    }
    private function createGithubService(array $responseData): GithubService
    {
        $this->mockResponse = new MockResponse(json_encode($responseData));
        $this->mockHttpClient->setResponseFactory($this->mockResponse);
        return new GithubService($this->mockHttpClient, $this->mockLogger);
    }
}