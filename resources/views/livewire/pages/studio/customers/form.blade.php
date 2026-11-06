<?php

use App\Models\Customer;
use App\Services\AuditLogger;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new class extends Component
{
    #[Locked]
    public ?int $customerId = null;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $notes = '';

    public function mount(?Customer $customer = null): void
    {
        if ($customer?->exists) {
            $this->authorize('update', $customer);
            $this->customerId = $customer->id;
            $this->name = $customer->name;
            $this->email = (string) $customer->email;
            $this->phone = (string) $customer->phone;
            $this->notes = (string) $customer->notes;
        } else {
            $this->authorize('create', Customer::class);
        }
    }

    public function rendering($view): void
    {
        $view->title($this->customerId ? __('Edit client') : __('Add client'));
    }

    public function save(AuditLogger $audit): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+()\-\s.]{6,40}$/'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], ['phone.regex' => 'Enter a valid phone number (digits, spaces, +, - and brackets).']);
        $validated = array_map(fn ($v) => $v === '' ? null : $v, $validated);

        if ($this->customerId) {
            $customer = Customer::visibleTo(auth()->user())->findOrFail($this->customerId);
            $this->authorize('update', $customer);
            $customer->fill($validated)->save();
            $audit->logChanges('customer.updated', $customer);
            $message = __('Client updated.');
        } else {
            $this->authorize('create', Customer::class);
            $customer = new Customer($validated);
            // Ownership always comes from the signed-in photographer, never from input.
            $customer->photographerProfile()->associate(auth()->user()->photographerProfile);
            $customer->save();
            $audit->log('customer.created', $customer, new: $customer->only(['name', 'email', 'phone']));
            $message = __('Client added.');
        }

        session()->flash('toast', ['message' => $message, 'type' => 'success']);
        $this->redirectRoute('studio.customers.index', navigate: true);
    }
}; ?>

<div>
    <a href="{{ route('studio.customers.index') }}" wire:navigate class="focus-ring mb-6 inline-flex items-center gap-2 rounded text-sm text-grey-500 transition hover:text-ink">
        <x-ui.icon name="arrow-left" class="h-4 w-4" /> {{ __('All clients') }}
    </a>

    <x-ui.page-header :eyebrow="__('Clients')" :title="$customerId ? __('Edit client') : __('Add a client')" />

    <form wire:submit="save" class="max-w-3xl">
        <x-ui.card>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.input name="name" :label="__('Full name')" wire:model="name" required autofocus class="sm:col-span-2" />
                <x-ui.input name="email" :label="__('Email')" type="email" wire:model="email" optional />
                <x-ui.input name="phone" :label="__('Phone')" type="tel" wire:model="phone" optional />
                <x-ui.textarea name="notes" :label="__('Notes')" wire:model="notes" optional rows="4" class="sm:col-span-2"
                    :hint="__('Private to your studio. For example: wedding date, preferences.')" />
            </div>
            <x-slot name="footer">
                <x-ui.button variant="secondary" :href="route('studio.customers.index')" wire:navigate>{{ __('Cancel') }}</x-ui.button>
                <x-ui.button wire:loading.attr="disabled">{{ $customerId ? __('Save changes') : __('Add client') }}</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</div>
