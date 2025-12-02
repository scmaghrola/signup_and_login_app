<?php
 
namespace App\Livewire;
 
use Livewire\Component;
 
class Counter extends Component
{
    public $count = 1;
 
    public function increment()
    {
        $this->count++;
    }
 
    public function decrement()
    {
        $this->count--;
    }
 
    public function render()
    {
        info("counter render called");
        return view('livewire.counter')->layout('layouts.app');
    }
}