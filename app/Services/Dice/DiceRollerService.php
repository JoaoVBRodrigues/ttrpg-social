<?php

namespace App\Services\Dice;

use App\Enums\DiceRollMode;
use App\Enums\MessageType;
use App\Events\CampaignMessageCreated;
use App\Exceptions\DomainException;
use App\Models\Campaign;
use App\Models\DiceRoll;
use App\Models\Message;
use App\Models\User;
use App\Services\Message\MessageService;
use Illuminate\Support\Facades\DB;

class DiceRollerService
{
    public function __construct(
        protected MessageService $messageService,
    ) {
    }

    public function execute(User $user, Campaign $campaign, array $data): DiceRoll
    {
        $this->messageService->ensureActiveMember($user, $campaign);
        $parsed = $this->parseExpression($data['expression']);

        return DB::transaction(function () use ($user, $campaign, $data, $parsed): DiceRoll {
            $diceResults = [];

            foreach (range(1, $parsed['count']) as $rollIndex) {
                $diceResults[] = random_int(1, $parsed['sides']);
            }

            if ($parsed['mode'] === DiceRollMode::ADVANTAGE || $parsed['mode'] === DiceRollMode::DISADVANTAGE) {
                $diceResults[] = random_int(1, $parsed['sides']);
            }

            $kept = $diceResults;

            if ($parsed['mode'] === DiceRollMode::ADVANTAGE) {
                rsort($kept);
                $kept = [max($diceResults)];
            } elseif ($parsed['mode'] === DiceRollMode::DISADVANTAGE) {
                sort($kept);
                $kept = [min($diceResults)];
            }

            $total = array_sum($kept) + $parsed['modifier'];

            $message = Message::query()->create([
                'campaign_id' => $campaign->getKey(),
                'user_id' => $user->getKey(),
                'session_id' => $data['session_id'] ?? null,
                'type' => MessageType::DICE_ROLL,
                'content' => "{$user->name} rolled {$parsed['normalized']} = {$total}",
                'metadata' => [
                    'expression' => $parsed['normalized'],
                ],
            ]);

            $roll = DiceRoll::query()->create([
                'campaign_id' => $campaign->getKey(),
                'session_id' => $data['session_id'] ?? null,
                'user_id' => $user->getKey(),
                'message_id' => $message->getKey(),
                'expression' => $data['expression'],
                'normalized_expression' => $parsed['normalized'],
                'dice_results' => [
                    'all' => $diceResults,
                    'kept' => $kept,
                ],
                'modifiers' => ['modifier' => $parsed['modifier']],
                'total' => $total,
                'roll_mode' => $parsed['mode'],
                'rolled_at' => now(),
            ]);

            CampaignMessageCreated::dispatch($message->load('user'));

            return $roll->load('message');
        });
    }

    /**
     * @return array{count:int,sides:int,modifier:int,mode:DiceRollMode,normalized:string}
     */
    public function parseExpression(string $expression): array
    {
        $value = strtolower(trim($expression));

        if (! preg_match('/^(?<count>\d+)d(?<sides>\d+)(?<modifier>[+-]\d+)?(?:\s+(?<mode>adv|dis))?$/', $value, $matches)) {
            throw new DomainException('The dice expression format is invalid.');
        }

        $count = (int) $matches['count'];
        $sides = (int) $matches['sides'];
        $modifier = isset($matches['modifier']) && $matches['modifier'] !== '' ? (int) $matches['modifier'] : 0;
        $mode = match ($matches['mode'] ?? null) {
            'adv' => DiceRollMode::ADVANTAGE,
            'dis' => DiceRollMode::DISADVANTAGE,
            default => DiceRollMode::NORMAL,
        };

        if ($count < 1 || $count > 20 || $sides < 2 || $sides > 1000 || abs($modifier) > 100) {
            throw new DomainException('The dice expression exceeds supported limits.');
        }

        if ($mode !== DiceRollMode::NORMAL && ! ($count === 1 && $sides === 20)) {
            throw new DomainException('Advantage and disadvantage are only supported for 1d20.');
        }

        $normalized = "{$count}d{$sides}".($modifier !== 0 ? ($modifier > 0 ? "+{$modifier}" : (string) $modifier) : '');
        if ($mode === DiceRollMode::ADVANTAGE) {
            $normalized .= ' adv';
        }
        if ($mode === DiceRollMode::DISADVANTAGE) {
            $normalized .= ' dis';
        }

        return [
            'count' => $count,
            'sides' => $sides,
            'modifier' => $modifier,
            'mode' => $mode,
            'normalized' => $normalized,
        ];
    }
}
