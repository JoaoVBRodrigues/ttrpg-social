<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Concerns\HandlesDomainExceptions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Resources\Message\MessageResource;
use App\Models\Campaign;
use App\Services\Message\MessageService;
use Illuminate\Http\RedirectResponse;

class CampaignMessageController extends Controller
{
    use HandlesDomainExceptions;

    public function store(StoreMessageRequest $request, Campaign $campaign, MessageService $service)
    {
        return $this->handleAction($request, function () use ($request, $campaign, $service) {
            $message = $service->postTextMessage($request->user(), $campaign, $request->validated());

            if ($request->expectsJson()) {
                return new MessageResource($message);
            }

            return redirect()
                ->route('campaigns.show', $campaign)
                ->with('status', 'campaign-message-created');
        });
    }
}
