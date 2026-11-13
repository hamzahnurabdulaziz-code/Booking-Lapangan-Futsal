<?php

namespace App\Livewire\PublicPages;

use App\Models\Field;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Daftar Lapangan — FutsalKita')]
class FieldList extends Component
{
    #[Url]
    public string $type = 'all'; // all, indoor, outdoor

    #[Url]
    public string $status = 'active'; // active, all

    public function render()
    {
        $query = Field::query();

        if ($this->type !== 'all' && in_array($this->type, ['indoor', 'outdoor'])) {
            $query->where('type', $this->type);
        }

        if ($this->status === 'active') {
            $query->where('is_active', true);
        }

        $fields = $query->orderBy('is_active', 'desc')->orderBy('name')->get();

        return view('livewire.public-pages.field-list', [
            'fields' => $fields,
        ]);
    }
}
