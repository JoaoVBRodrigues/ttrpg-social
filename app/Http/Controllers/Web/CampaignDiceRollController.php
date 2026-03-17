<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Concerns\HandlesDomainExceptions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dice\StoreDiceRollRequest;
use App\Http\Resources\Dice\DiceRollResource;
use App\Models\Campaign;
use App\Services\Dice\DiceRollerService;
use Illuminate\Http\RedirectResponse;

class CampaignDiceRollController extends Controller
{
    use HandlesDomainExceptions;

    public function store(StoreDiceRollRequest $request, Campaign $campaign, DiceRollerService $service)
    {
        return $this->handleAction($request, function () use ($request, $campaign, $service) {
            $roll = $service->execute($request->user(), $campaign, $request->validated());

            if ($request->expectsJson()) {
                return new DiceRollResource($roll);
            }

            return redirect()
                ->route('campaigns.show', $campaign)
                ->with('status', 'campaign-roll-created');
        });
    }
}
