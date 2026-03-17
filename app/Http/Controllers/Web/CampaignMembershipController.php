<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Concerns\HandlesDomainExceptions;
use App\Http\Controllers\Controller;
use App\Http\Requests\CampaignMember\ReviewCampaignMemberRequest;
use App\Http\Requests\CampaignMember\StoreCampaignInviteRequest;
use App\Models\Campaign;
use App\Models\CampaignMember;
use App\Services\Campaign\CampaignMembershipService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CampaignMembershipController extends Controller
{
    use HandlesDomainExceptions;

    public function requestJoin(Request $request, Campaign $campaign, CampaignMembershipService $service): RedirectResponse
    {
        $this->authorize('requestJoin', $campaign);

        return $this->handleAction($request, function () use ($request, $campaign, $service) {
            $service->requestToJoin($request->user(), $campaign);

            return redirect()
                ->route('campaigns.show', $campaign)
                ->with('status', 'campaign-join-requested');
        });
    }

    public function invite(StoreCampaignInviteRequest $request, Campaign $campaign, CampaignMembershipService $service): RedirectResponse
    {
        $this->authorize('manageMembers', $campaign);

        return $this->handleAction($request, function () use ($request, $campaign, $service) {
            $service->inviteByUsername(
                $request->user(),
                $campaign,
                $request->validated('username'),
                $request->validated('role', 'player'),
            );

            return redirect()
                ->route('campaigns.show', $campaign)
                ->with('status', 'campaign-member-invited');
        });
    }

    public function review(ReviewCampaignMemberRequest $request, CampaignMember $membership, CampaignMembershipService $service): RedirectResponse
    {
        $campaign = $membership->campaign;
        $this->authorize('manageMembers', $campaign);

        return $this->handleAction($request, function () use ($request, $membership, $campaign, $service) {
            $service->reviewMembership($membership, $request->validated('status'));

            return redirect()
                ->route('campaigns.show', $campaign)
                ->with('status', 'campaign-member-reviewed');
        });
    }

    public function remove(Request $request, CampaignMember $membership, CampaignMembershipService $service): RedirectResponse
    {
        $campaign = $membership->campaign;
        $this->authorize('manageMembers', $campaign);

        return $this->handleAction($request, function () use ($membership, $campaign, $service) {
            $service->removeMember($membership);

            return redirect()
                ->route('campaigns.show', $campaign)
                ->with('status', 'campaign-member-removed');
        });
    }

    public function leave(Request $request, Campaign $campaign, CampaignMembershipService $service): RedirectResponse
    {
        return $this->handleAction($request, function () use ($request, $campaign, $service) {
            $service->leaveCampaign($request->user(), $campaign);

            return redirect()
                ->route('campaigns.index')
                ->with('status', 'campaign-left');
        });
    }
}
