<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Dinosaur;
use App\Enum\HealthyStatus;
use Generator;
use PHPUnit\Framework\TestCase;

class DinosaurTest extends TestCase
{
    /**
     * @return void
     */
    public function testCanGetAndSetData(): void {
        $dino = new Dinosaur(
            name: 'Big Eat',
            genus: 'Tyrannosaurus',
            length: 15,
            enclosure: 'Paddock A'
        );

        self::assertSame('Big Eat', $dino->getName());
        self::assertSame('Tyrannosaurus', $dino->getGenus());
        self::assertSame(15, $dino->getLength());
        self::assertSame('Paddock A', $dino->getEnclosure());
    }

    /**
     * @dataProvider sizeDescriptionProvider();
     * @param int $length
     * @param string $expectedSize
     * @return void
     */
    public function testDino10MetersOrGreaterIsLarge(int $length, string $expectedSize): void
    {
        $dino = new Dinosaur(name: 'Big Eaty', length: $length);
        self::assertSame($expectedSize, $dino->getSizeDescription());
    }

    /**
     * @return Generator
     */
    public function sizeDescriptionProvider(): Generator
    {
        yield '10 Meters Large Dino' => [10, 'Large'];
        yield '5 Meters Medium Dino' => [5, 'Medium'];
        yield '4 Meters Small Dino' => [4, 'Small'];
    }

    public function testIsAcceptingVisitorsByDefault(): void {
        $dino = new Dinosaur('Dennis');
        self::assertTrue($dino->isAcceptingVisitors());
    }

    /**
     * @dataProvider healthStatusProvider()
     * @param HealthyStatus $healthyStatus
     * @param bool $expectedVisitorStatus
     * @return void
     */
    public function testIsAcceptingVisitorsBasedOnHealthStatus(HealthyStatus $healthyStatus, bool $expectedVisitorStatus) : void
    {
        $dino = new Dinosaur('Bumpy');
        $dino->setHealth($healthyStatus);
        self::assertSame($expectedVisitorStatus, $dino->isAcceptingVisitors());
    }

    /**
     * @return Generator
     */
    public function healthStatusProvider(): Generator {
        yield 'Sick dino is not accepting visitors' => [HealthyStatus::SICK, false];
        yield 'Hungry dino is accepting visitors' => [HealthyStatus::HUNGRY, true];
    }

}