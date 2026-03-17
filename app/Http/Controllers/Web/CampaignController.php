<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Concerns\HandlesDomainExceptions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\StoreCampaignRequest;
use App\Http\Requests\Campaign\UpdateCampaignRequest;
use App\Http\Resources\Campaign\CampaignCollection;
use App\Http\Resources\Campaign\CampaignResource;
use App\Models\Campaign;
use App\Models\GameSystem;
use App\Services\Campaign\CampaignService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignController extends Controller
{
    use HandlesDomainExceptions;

    public function __construct()
    {
        $this->authorizeResource(Campaign::class, 'campaign');
    }

    public function index(Request $request, CampaignService $service): View|CampaignCollection
    {
        if ($request->expectsJson()) {
            return new CampaignCollection($service->paginatePublicCampaigns($request->only([
                'search',
                'status',
                'system',
                'language',
                'open_only',
            ])));
        }

        return view('campaigns.index', [
            'gameSystems' => GameSystem::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('campaigns.create', [
            'gameSystems' => GameSystem::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreCampaignRequest $request, CampaignService $service): RedirectResponse|CampaignResource
    {
        return $this->handleAction($request, function () use ($request, $service) {
            $campaign = $service->createCampaign($request->user(), $request->validated());

            if ($request->expectsJson()) {
                return new CampaignResource($campaign);
            }

            return redirect()
                ->route('campaigns.show', $campaign)
                ->with('status', 'campaign-created');
        });
    }

    public function show(Request $request, Campaign $campaign): View|CampaignResource
    {
        $campaign->load(['owner', 'gameSystem', 'members.user'])->loadCount('members');

        if ($request->expectsJson()) {
            return new CampaignResource($campaign);
        }

        return view('campaigns.show', [
            'campaign' => $campaign,
        ]);
    }

    public function edit(Campaign $campaign): View
    {
        return view('campaigns.edit', [
            'campaign' => $campaign,
            'gameSystems' => GameSystem::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateCampaignRequest $request, Campaign $campaign, CampaignService $service): RedirectResponse|CampaignResource
    {
        return $this->handleAction($request, function () use ($request, $campaign, $service) {
            $campaign = $service->updateCampaign($campaign, $request->validated());

            if ($request->expectsJson()) {
                return new CampaignResource($campaign);
            }

            return redirect()
                ->route('campaigns.show', $campaign)
                ->with('status', 'campaign-updated');
        });
    }
}
