<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Contact;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class ContactManager extends Component
{

    use WithPagination, WithFileUploads;

    public $name, $email, $message, $contact_id;
    public $photo;
    public $search = '';
    public $isEdit = false;

    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $limit = 5;

    public $sortOption = 'id asc';

    

    protected $listeners = ['contactAdded' => 'refreshList'];

    protected $queryString = [
        'search' => ['except' => ''],
        // 'sortField' => ['except' => 'id'],
        // 'sortDirection' => ['except' => 'asc'],
        'sortOption' => ['except' => 'id asc'],
        'limit' => ['except' => '5'],
    ];

    public function updatingSearch()
    {
        info('updatingSearch called');
        $this->resetPage(); // reset pagination on search
    }

    public function render()
    {

        info('render calledddddddddddddddddd');
        $contacts = Contact::when($this->search, function ($query) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('message', 'like', $term);
            });
        });

        // $sort = request()->get('sort') ?? 'name_asc';
        // if ($sort == 'name_asc') {
        //     $contacts = $contacts->orderBy('name', 'ASC');
        // } elseif ($sort == 'name_desc') {
        //     $contacts = $contacts->orderBy('name', 'DESC');
        // } elseif ($sort == 'update_at_asc') {
        //     $contacts = $contacts->orderBy('update_at', 'ASC');
        // } elseif ($sort == 'update_at_desc') {
        //     $contacts = $contacts->orderBy('update_at', 'DESC');
        // } elseif ($sort == 'id_asc') {
        //     $contacts = $contacts->orderBy('id', 'ASC');
        // }

        // $contacts = $contacts->orderBy($this->sortField, $this->sortDirection);
        $contacts = $contacts->orderByRaw($this->sortOption);

        $contacts = $contacts->paginate($this->limit);

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

        $contact = Contact::create($data);

        session()->flash('success', 'Contact Added Successfully');

        $this->dispatch('contactAdded', $contact);

        $this->reset(['name', 'email', 'message', 'photo']);
    }

    public function refreshList($contact)
    {
        info("New contact added: ");
        info($contact);
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

    public function updatedSortOption($value)
    {
        if (str_starts_with($value, '-')) {
            $this->sortDirection = 'desc';
            $this->sortField = ltrim($value, '-');
        } else {
            $this->sortDirection = 'asc';
            $this->sortField = $value;
        }

        $this->resetPage();
    }




    public function delete($id)
    {

        $currentPage = $this->getPage();

        $contact = Contact::findOrFail($id);
        if ($contact->photo) {
            Storage::disk('public')->delete($contact->photo);
        }
        $contact->delete();

        $contacts = Contact::when($this->search, function ($query) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('message', 'like', $term);
            });
        })
            ->orderBy('id', 'ASC')
            ->paginate($this->limit);

        if ($currentPage > $contacts->lastPage()) {
            $this->setPage($currentPage - 1);
        } else {
            info('elseeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee');
        }

        session()->flash('success', 'Contact Deleted Successfully');
    }

    // public function sortBy($field)
    // {
    //     // if clicking the same field → toggle ASC/DESC
    //     if ($this->sortField === $field) {
    //         $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    //     } else {
    //         // new field → reset to ASC
    //         $this->sortField = $field;
    //         $this->sortDirection = 'asc';
    //     }

    //     // Reset to page 1 when sorting changes
    //     $this->resetPage();
    // }

    // public function sortOption()
    // {
        
    // }


}
