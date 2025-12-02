<div>
    <h1>{{ $count }}</h1>
 
    <p style="color:blue; font-style:bold">{{ rand(100,999) }}</p>
    <button wire:click="increment">+</button>
 
    <button wire:click="decrement">-</button>
</div>
