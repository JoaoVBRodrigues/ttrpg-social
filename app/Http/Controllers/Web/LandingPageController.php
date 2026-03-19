<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CampaignMember;
use App\Models\GameSystem;
use App\Models\User;
use App\Services\Campaign\CampaignService;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function __invoke(CampaignService $service): View
    {
        return view('welcome', [
            'featuredCampaigns' => $service->queryPublicCampaigns([
                'open_only' => true,
            ])->take(3)->get(),
            'highlights' => [
                'campaigns' => $service->queryPublicCampaigns()->count(),
                'systems' => GameSystem::query()->count(),
                'players' => User::query()->count(),
                'memberships' => CampaignMember::query()->where('status', 'active')->count(),
            ],
        ]);
    }
}
