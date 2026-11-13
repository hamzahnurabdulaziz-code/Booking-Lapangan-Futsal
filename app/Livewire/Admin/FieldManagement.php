<?php

namespace App\Livewire\Admin;

use App\Models\Field;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Manajemen Lapangan — FutsalKita')]
class FieldManagement extends Component
{
    public bool $showModal = false;
    public ?int $editingFieldId = null;

    // Form inputs
    public string $name = '';
    public string $slug = '';
    public string $type = 'indoor';
    public string $description = '';
    public string $facilitiesText = '';
    public string $imagePath = '';
    public bool $isActive = true;

    /**
     * Open modal to create a new field.
     */
    public function createField(): void
    {
        $this->reset(['editingFieldId', 'name', 'slug', 'type', 'description', 'facilitiesText', 'imagePath', 'isActive']);
        $this->isActive = true;
        $this->type = 'indoor';
        $this->showModal = true;
    }

    /**
     * Open modal to edit an existing field.
     */
    public function editField(int $fieldId): void
    {
        $field = Field::findOrFail($fieldId);

        $this->editingFieldId = $field->id;
        $this->name = $field->name;
        $this->slug = $field->slug;
        $this->type = $field->type;
        $this->description = $field->description ?? '';
        $this->facilitiesText = is_array($field->facilities) ? implode("\n", $field->facilities) : '';
        $this->imagePath = $field->image_path ?? '';
        $this->isActive = (bool) $field->is_active;

        $this->showModal = true;
    }

    /**
     * Save the field (create or update).
     */
    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:indoor,outdoor'],
            'description' => ['nullable', 'string', 'max:1000'],
            'facilitiesText' => ['nullable', 'string'],
            'imagePath' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama lapangan wajib diisi.',
            'type.required' => 'Tipe lapangan wajib dipilih.',
        ]);

        $facilities = array_filter(
            array_map('trim', explode("\n", $this->facilitiesText))
        );

        $slug = !empty($this->slug) ? Str::slug($this->slug) : Str::slug($this->name);

        if ($this->editingFieldId) {
            $field = Field::findOrFail($this->editingFieldId);
            $field->update([
                'name' => trim($this->name),
                'slug' => $slug,
                'type' => $this->type,
                'description' => trim($this->description),
                'facilities' => array_values($facilities),
                'image_path' => trim($this->imagePath) ?: null,
                'is_active' => $this->isActive,
            ]);

            session()->flash('success', "Lapangan {$field->name} berhasil diperbarui.");
        } else {
            // Ensure unique slug
            $baseSlug = $slug;
            $counter = 1;
            while (Field::where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }

            $field = Field::create([
                'name' => trim($this->name),
                'slug' => $slug,
                'type' => $this->type,
                'description' => trim($this->description),
                'facilities' => array_values($facilities),
                'image_path' => trim($this->imagePath) ?: 'images/placeholder-field.svg',
                'is_active' => $this->isActive,
            ]);

            session()->flash('success', "Lapangan baru {$field->name} berhasil ditambahkan.");
        }

        $this->showModal = false;
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(int $fieldId): void
    {
        $field = Field::findOrFail($fieldId);
        $field->is_active = !$field->is_active;
        $field->save();

        $statusLabel = $field->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('success', "Lapangan {$field->name} berhasil {$statusLabel}.");
    }

    public function render()
    {
        $fields = Field::withCount('bookings')->orderBy('id')->get();

        return view('livewire.admin.field-management', [
            'fields' => $fields,
        ]);
    }
}
