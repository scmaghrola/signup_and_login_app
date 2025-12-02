<div>
    <form wire:submit.prevent="submitForm">

        <div>
            <label>Name:</label>
            <input type="text" wire:model="name" class="form-control">
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mt-2">
            <label>Email:</label>
            <input type="email" wire:model="email" class="form-control">
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mt-2">
            <label>Message:</label>
            <textarea wire:model="message" class="form-control" rows="3"></textarea>
            @error('message')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mt-2">
            <label>Photo:</label>
            <input type="file" wire:model="photo" class="form-control">

            @error('photo')
                <span class="text-danger">{{ $message }}</span>
            @enderror

            {{-- Preview --}}
            @if ($photo)
                <img src="{{ $photo->temporaryUrl() }}" width="120" class="mt-2">
            @endif
        </div>

        <button class="btn btn-primary mt-3">Submit</button>

    </form>

    @if (session()->has('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
    {{-- <div class="mt-3">
        <livewire:counter />
    </div> --}}
</div>
