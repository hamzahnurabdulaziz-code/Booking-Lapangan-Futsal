<?php

namespace App\Livewire\PublicPages;

use App\Models\Field;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('FutsalKita — Jadwal Main Aman, Tinggal Ajak Tim')]
class Home extends Component
{
    public ?int $selectedFieldId = null;
    public string $selectedDate = '';
    public int $startHour = 19;
    public int $durationHours = 1;

    public function mount(): void
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $this->selectedDate = Carbon::now($tz)->toDateString();

        $firstField = Field::active()->first();
        if ($firstField) {
            $this->selectedFieldId = $firstField->id;
        }
    }

    /**
     * Submit search form and redirect to field detail with preselected schedule.
     */
    public function checkSchedule(): void
    {
        $this->validate([
            'selectedFieldId' => ['required', 'exists:fields,id'],
            'selectedDate' => ['required', 'date'],
            'startHour' => ['required', 'integer', 'min:8', 'max:22'],
            'durationHours' => ['required', 'integer', 'min:1', 'max:3'],
        ], [
            'selectedFieldId.required' => 'Pilih lapangan terlebih dahulu.',
            'selectedDate.required' => 'Pilih tanggal bermain.',
            'startHour.required' => 'Pilih jam mulai.',
            'durationHours.required' => 'Pilih durasi bermain.',
        ]);

        $field = Field::findOrFail($this->selectedFieldId);

        $this->redirectRoute('fields.show', [
            'field' => $field->slug,
            'date' => $this->selectedDate,
            'hour' => $this->startHour,
            'duration' => $this->durationHours,
        ], navigate: true);
    }

    public function render()
    {
        $fields = Field::active()->get();
        $allFieldsForSelect = Field::all();

        return view('livewire.public-pages.home', [
            'fields' => $fields,
            'allFieldsForSelect' => $allFieldsForSelect,
        ]);
    }
}
