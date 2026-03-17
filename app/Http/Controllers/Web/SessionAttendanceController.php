<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Concerns\HandlesDomainExceptions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Session\RespondSessionAttendanceRequest;
use App\Models\CampaignSession;
use App\Services\Session\SessionAttendanceService;
use Illuminate\Http\RedirectResponse;

class SessionAttendanceController extends Controller
{
    use HandlesDomainExceptions;

    public function update(RespondSessionAttendanceRequest $request, CampaignSession $campaignSession, SessionAttendanceService $service): RedirectResponse
    {
        return $this->handleAction($request, function () use ($request, $campaignSession, $service) {
            $service->respond($request->user(), $campaignSession, $request->validated());

            return redirect()
                ->route('campaigns.show', $campaignSession->campaign)
                ->with('status', 'session-attendance-updated');
        });
    }
}
