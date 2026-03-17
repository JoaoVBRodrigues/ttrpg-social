<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Concerns\HandlesDomainExceptions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Session\StoreCampaignSessionRequest;
use App\Http\Requests\Session\UpdateCampaignSessionRequest;
use App\Http\Resources\Session\CampaignSessionResource;
use App\Models\Campaign;
use App\Models\CampaignSession;
use App\Services\Session\CampaignSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CampaignSessionController extends Controller
{
    use HandlesDomainExceptions;

    public function store(StoreCampaignSessionRequest $request, Campaign $campaign, CampaignSessionService $service)
    {
        abort_unless($service->canManage($request->user(), $campaign), 403);

        return $this->handleAction($request, function () use ($request, $campaign, $service) {
            $session = $service->createSession($request->user(), $campaign, $request->validated());

            if ($request->expectsJson()) {
                return new CampaignSessionResource($session);
            }

            return redirect()
                ->route('campaigns.show', $campaign)
                ->with('status', 'campaign-session-created');
        });
    }

    public function update(UpdateCampaignSessionRequest $request, CampaignSession $campaignSession, CampaignSessionService $service)
    {
        abort_unless($service->canManage($request->user(), $campaignSession->campaign), 403);

        return $this->handleAction($request, function () use ($request, $campaignSession, $service) {
            $session = $service->updateSession($request->user(), $campaignSession, $request->validated());

            if ($request->expectsJson()) {
                return new CampaignSessionResource($session);
            }

            return redirect()
                ->route('campaigns.show', $campaignSession->campaign)
                ->with('status', 'campaign-session-updated');
        });
    }
}
