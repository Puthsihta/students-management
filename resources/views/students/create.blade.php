@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Add New Student</h2>

    <form action="{{ route('students.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block font-medium">Name</label>
            <input type="text" name="name" class="w-full border p-2 rounded" value="{{ old('name') }}">
            @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block font-medium">Email</label>
            <input type="email" name="email" class="w-full border p-2 rounded" value="{{ old('email') }}">
            @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block font-medium">Age</label>
            <input type="number" name="age" class="w-full border p-2 rounded" value="{{ old('age') }}">
            @error('age') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Create</button>
        <a href="{{ route('students.index') }}" class="ml-2 text-gray-600 hover:underline">Cancel</a>
    </form>
</div>
@endsection