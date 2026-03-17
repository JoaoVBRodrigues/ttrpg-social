<?php

namespace App\Livewire\Sessions;

use App\Models\Campaign;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class RsvpPanel extends Component
{
    public Campaign $campaign;

    public function mount(Campaign $campaign): void
    {
        $this->campaign = $campaign;
    }

    public function render(): View
    {
        $campaign = $this->campaign->load([
            'sessions' => fn ($query) => $query->orderBy('starts_at')->with([
                'attendances' => fn ($attendanceQuery) => $attendanceQuery->where('user_id', auth()->id()),
            ]),
        ]);

        return view('livewire.sessions.rsvp-panel', [
            'sessions' => $campaign->sessions,
        ]);
    }
}
