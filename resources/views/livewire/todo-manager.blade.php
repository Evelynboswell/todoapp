<?php

use Livewire\Volt\Component;
use App\Models\Todo;

new class extends Component {
    public Todo $todo;
    public string $todoName = '';

    public array $finishedTodos = [];

    public function createTodo()
    {
        $this->validate([
            'todoName' => 'required|min:3',
        ]);

        Auth::user()
            ->todos()
            ->create([
                'name' => $this->pull('todoName'),
            ]);
    }

    public function deleteTodo(int $id)
    {
        $todo = Todo::find($id);
        $this->authorize('delete', $todo);
        $todo->delete();

        return redirect()->route('dashboard');
    }

    public function finishTodo(int $id)
    {
        $todo = Todo::find($id);
        $this->authorize('update', $todo);
        $todo->finished = true;
        $todo->save();
    }

    public function with()
    {
        return [
            'todos' => Todo::orderBy('id', 'desc')->get(),
        ];
    }
};
?>

<div>
    <form wire:submit='createTodo' class="flex items-center justify-between mb-8 space-x-8">
        <x-text-input wire:model='todoName' class="flex-1 w-full" />
        <x-primary-button type="submit">Create</x-primary-button>
        <x-input-error :messages="$errors->get('todoName')" class="mt-2" />
    </form>

    @foreach ($todos as $todo)
        <div wire:transition wire:key='{{ $todo->id }}'
            class="flex items-center justify-between p-4 m-auto mb-4 space-x-4 bg-white border border-gray-200 rounded-lg shadow-md ">

            <a href="{{ route('todo', $todo->id) }}" class="flex items-center justify-between space-x-4">
                <div class="text-sm font-medium text-gray-800">
                    {{ $todo->name }}
                </div>
            </a>

            <div class="flex items-center justify-between space-x-4">
                @if(!$todo->finished)
                    <x-primary-button wire:click='finishTodo({{ $todo->id }})'>Finish</x-primary-button>
                @else
                    <button class="bg-green-500 text-white py-2 px-4 rounded cursor-not-allowed" disabled>Finished</button>
                @endif

                <x-danger-button wire:click='deleteTodo({{ $todo->id }})' class="flex-shrink-0">Delete</x-danger-button>
            </div>
        </div>
    @endforeach
</div>