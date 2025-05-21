@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Student Details</h2>

    <p><strong>Name:</strong> {{ $student->name }}</p>
    <p><strong>Email:</strong> {{ $student->email }}</p>
    <p><strong>Age:</strong> {{ $student->age }}</p>

    <div class="mt-4">
        <a href="{{ route('students.edit', $student) }}" class="text-yellow-600 hover:underline mr-4">Edit</a>
        <a href="{{ route('students.index') }}" class="text-gray-600 hover:underline">Back</a>
    </div>
</div>
@endsection