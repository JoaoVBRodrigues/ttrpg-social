<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Concerns\HandlesDomainExceptions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\StoreCampaignReferenceRequest;
use App\Http\Requests\Campaign\UpdateCampaignReferenceRequest;
use App\Http\Resources\CampaignReference\CampaignReferenceResource;
use App\Models\Campaign;
use App\Models\CampaignReference;
use App\Services\Campaign\CampaignReferenceService;
use Illuminate\Http\Request;

class CampaignReferenceController extends Controller
{
    use HandlesDomainExceptions;

    public function store(StoreCampaignReferenceRequest $request, Campaign $campaign, CampaignReferenceService $service)
    {
        $this->authorize('update', $campaign);

        return $this->handleAction($request, function () use ($request, $campaign, $service) {
            $reference = $service->createReference($request->user(), $campaign, $request->validated());

            if ($request->expectsJson()) {
                return new CampaignReferenceResource($reference);
            }

            return redirect()
                ->route('campaigns.show', $campaign)
                ->with('status', 'campaign-reference-created');
        });
    }

    public function update(UpdateCampaignReferenceRequest $request, CampaignReference $campaignReference, CampaignReferenceService $service)
    {
        $this->authorize('update', $campaignReference->campaign);

        return $this->handleAction($request, function () use ($request, $campaignReference, $service) {
            $reference = $service->updateReference($campaignReference, $request->validated());

            if ($request->expectsJson()) {
                return new CampaignReferenceResource($reference);
            }

            return redirect()
                ->route('campaigns.show', $campaignReference->campaign)
                ->with('status', 'campaign-reference-updated');
        });
    }

    public function destroy(Request $request, CampaignReference $campaignReference, CampaignReferenceService $service)
    {
        $this->authorize('update', $campaignReference->campaign);

        return $this->handleAction($request, function () use ($request, $campaignReference, $service) {
            $campaign = $campaignReference->campaign;
            $service->deleteReference($campaignReference);

            if ($request->expectsJson()) {
                return response()->noContent();
            }

            return redirect()
                ->route('campaigns.show', $campaign)
                ->with('status', 'campaign-reference-deleted');
        });
    }
}
