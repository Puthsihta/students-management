@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Students</h1>

    <a href="{{ route('students.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Add New Student</a>

    <table class="w-full mt-6 border">
        <thead class="bg-gray-200 text-left">
            <tr>
                <th class="px-4 py-2 border">Name</th>
                <th class="px-4 py-2 border">Email</th>
                <th class="px-4 py-2 border">Age</th>
                <th class="px-4 py-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $student->name }}</td>
                <td class="px-4 py-2">{{ $student->email }}</td>
                <td class="px-4 py-2">{{ $student->age }}</td>
                <td class="px-4 py-2">
                    <a href="{{ route('students.show', $student) }}" class="text-blue-600 hover:underline mr-2">View</a>
                    <a href="{{ route('students.edit', $student) }}" class="text-yellow-600 hover:underline mr-2">Edit</a>
                    <form action="{{ route('students.destroy', $student) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-4 py-4 text-center">No students found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection