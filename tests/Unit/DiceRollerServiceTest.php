<?php

namespace Tests\Unit;

use App\Enums\DiceRollMode;
use App\Exceptions\DomainException;
use App\Services\Dice\DiceRollerService;
use App\Services\Message\MessageService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DiceRollerServiceTest extends TestCase
{
    protected DiceRollerService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new DiceRollerService(new MessageService());
    }

    #[Test]
    public function it_normalizes_supported_dice_expressions(): void
    {
        $parsed = $this->service->parseExpression(' 1D20+4 ADV ');

        $this->assertSame(1, $parsed['count']);
        $this->assertSame(20, $parsed['sides']);
        $this->assertSame(4, $parsed['modifier']);
        $this->assertSame(DiceRollMode::ADVANTAGE, $parsed['mode']);
        $this->assertSame('1d20+4 adv', $parsed['normalized']);
    }

    #[Test]
    public function it_rejects_expressions_that_exceed_supported_limits(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('The dice expression exceeds supported limits.');

        $this->service->parseExpression('21d6');
    }

    #[Test]
    public function it_rejects_advantage_for_non_d20_rolls(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Advantage and disadvantage are only supported for 1d20.');

        $this->service->parseExpression('2d20 adv');
    }
}
