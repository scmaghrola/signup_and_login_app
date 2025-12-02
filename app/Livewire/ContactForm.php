<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Contact;
use Livewire\WithFileUploads;
class ContactForm extends Component
{
    
    use WithFileUploads;

    public $name;
    public $email;
    public $message;
    public $photo;

    public function render()
    {
        info('contactForm render called');
        return view('livewire.contact-form')->layout('layouts.app');
    }

    
    public function submitForm()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
            'message' => 'required|min:10',
            'photo' => 'nullable|image|max:2048', // max: 2MB
        ]);

        $photoPath = $this->photo?->store('photos', 'public');

        info("photoPath = $photoPath");

        Contact::create([
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
            'photo' => $photoPath,
        ]);

        session()->flash('success', 'Your message has been sent!');

        $this->reset(['name', 'email', 'message', 'photo']);
    }
}
