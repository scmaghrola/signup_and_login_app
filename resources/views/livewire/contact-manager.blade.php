<div class="container">

    <h2 class="mb-3">Contact Manager (CRUD + Pagination)</h2>

    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- SEARCH BAR --}}
    <div class="mb-3">
        <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search Name..." class="form-control">
        <div class="mt-2 text-muted small">
            Showing <strong>{{ $contacts->total() ?? 0 }}</strong> result(s)
            @if($search)
                for "<em>{{ $search }}</em>"
            @endif
        </div>
    </div>

    {{-- FORM FOR ADD/EDIT --}}
    <div class="card p-3 mb-4">
        <h4>{{ $isEdit ? 'Edit Contact' : 'Add Contact' }}</h4>

        <div class="mb-2">
            <input type="text" wire:model="name" placeholder="Name" class="form-control">
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-2">
            <input type="email" wire:model="email" placeholder="Email" class="form-control">
            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-2">
            <textarea wire:model="message" placeholder="Message" class="form-control"></textarea>
            @error('message') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-2">
            <label class="form-label">Photo (optional)</label>
            <input type="file" wire:model="photo" class="form-control">
            @if ($photo)
                <div class="mt-2">
                    <img src="{{ $photo->temporaryUrl() }}" alt="preview" style="max-width:120px; max-height:120px; object-fit:cover;" class="rounded">
                </div>
            @endif
            @error('photo') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div>
            @if ($isEdit)
                <button wire:click="update" class="btn btn-success">Update</button>
                <button wire:click="$set('isEdit', false); $reset(['name','email','message','photo'])" class="btn btn-secondary">Cancel</button>
            @else
                <button wire:click="store" class="btn btn-primary">Add</button>
            @endif
        </div>
    </div>

    {{-- CONTACT LIST --}}
    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Photo</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th width="180px">Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($contacts as $contact)
                <tr>
                    <td>{{ $contact->id }}</td>
                    <td>
                        @if ($contact->photo)
                            <img src="{{ asset('storage/' . $contact->photo) }}" alt="photo" style="width:56px;height:56px;object-fit:cover;border-radius:8px;">
                        @else
                            <div style="width:56px;height:56px;background:#f1f1f1;border-radius:8px;display:inline-block;"></div>
                        @endif
                    </td>
                    <td>{{ $contact->name }}</td>
                    <td>{{ $contact->email }}</td>
                    <td>{{ $contact->message }}</td>
                    <td>
                        <button wire:click="edit({{ $contact->id }})" class="btn btn-warning btn-sm">Edit</button>
                        <button wire:click="delete({{ $contact->id }})" class="btn btn-danger btn-sm">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- PAGINATION --}}
    {{ $contacts->links() }}

</div>
