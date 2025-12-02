<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Contact;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class ContactManager extends Component
{
    public $name, $email, $message, $contact_id;
    public $photo;
    public $search = '';
    public $isEdit = false;

    use WithPagination;
    use WithFileUploads;

    public function updatingSearch()
    {
        info('updatingSearch called');
        $this->resetPage(); // reset pagination on search
    }

    public function render()
    {
        $contacts = Contact::when($this->search, function ($query) {
            info('updatingSearch calledddddddddddddddddddddd');
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('email', 'like', $term)
                  ->orWhere('message', 'like', $term);
            });
        })
        ->orderBy('id', 'desc')
        ->paginate(5);

        return view('livewire.contact-manager', [
            'contacts' => $contacts,
        ])->layout('layouts.app');
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
            'message' => 'required|min:10',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
        ];

        if ($this->photo) {
            $path = $this->photo->store('contacts', 'public');
            $data['photo'] = $path;
        }

        Contact::create($data);

        session()->flash('success', 'Contact Added Successfully');

        $this->reset(['name', 'email', 'message', 'photo']);
    }

    public function edit($id)
    {
        $contact = Contact::findOrFail($id);
        $this->contact_id = $id;
        $this->name = $contact->name;
        $this->email = $contact->email;
        $this->message = $contact->message;
        $this->photo = null;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
            'message' => 'required|min:10',
        ]);

        $contact = Contact::findOrFail($this->contact_id);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
        ];

        if ($this->photo) {
            // delete old photo if exists
            if ($contact->photo) {
                Storage::disk('public')->delete($contact->photo);
            }
            $path = $this->photo->store('contacts', 'public');
            $data['photo'] = $path;
        }

        $contact->update($data);

        session()->flash('success', 'Contact Updated Successfully');

        $this->reset(['name', 'email', 'message', 'contact_id', 'photo']);
        $this->isEdit = false;
    }


    public function delete($id)
    {
        $contact = Contact::findOrFail($id);
        if ($contact->photo) {
            Storage::disk('public')->delete($contact->photo);
        }
        $contact->delete();

        session()->flash('success', 'Contact Deleted Successfully');
    }
}
