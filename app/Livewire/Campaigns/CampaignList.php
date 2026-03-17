<?php

namespace App\Livewire\Campaigns;

use App\Models\GameSystem;
use App\Services\Campaign\CampaignService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CampaignList extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'system')]
    public string $system = '';

    #[Url(as: 'language')]
    public string $language = '';

    #[Url(as: 'open_only')]
    public bool $openOnly = false;

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'status', 'system', 'language', 'openOnly'], true)) {
            $this->resetPage();
        }
    }

    public function render(CampaignService $service): View
    {
        return view('livewire.campaigns.campaign-list', [
            'campaigns' => $service->paginatePublicCampaigns([
                'search' => $this->search,
                'status' => $this->status,
                'system' => $this->system,
                'language' => $this->language,
                'open_only' => $this->openOnly,
            ], 9),
            'gameSystems' => GameSystem::query()->orderBy('name')->get(),
        ]);
    }
}
