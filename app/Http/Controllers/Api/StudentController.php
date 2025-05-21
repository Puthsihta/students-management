<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);

        $query = Student::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $students = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'message' => 'Students retrieved successfully',
            'data' => $students->items(),
            'meta' => [
                'page' => $students->currentPage(),
                'total_items' => $students->total(),
                'total_pages' => $students->lastPage(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:students,email',
                'age' => 'required|integer|min:1',
            ], [
                'name.required' => 'The name field is required.',
                'email.required' => 'The email field is required.',
                'email.email' => 'The email field must be a valid email address, e.g. "email@example.com".',
                'email.unique' => 'This email is already used by another student.',
                'age.required' => 'The age field is required.',
                'age.integer' => 'The age must be a number.',
                'age.min' => 'The age must be at least 1.',
            ],);

            $student = Student::create($validated);

            return response()->json([
                'message' => 'Student created successfully',
                'data' => $student
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $student = Student::findOrFail($id);

            // dump("get student with ID: {$id}");
            return response()->json([
                'message' => 'Student retrieved successfully',
                'data' => $student,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Student not found',
                'data' => null,
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (!$id) {
            return response()->json([
                'message' => 'Student ID is required in the request URL.',
                'data' => null,
            ], 400);
        }

        try {
            $student = Student::findOrFail($id);

            // Validate input with rules similar to store()
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:students,email,' . $id,
                'age' => 'sometimes|required|integer|min:1',
            ], [
                'name.required' => 'The name field is required.',
                'email.required' => 'The email field is required.',
                'email.email' => 'The email must be a valid email address, e.g. "example@gmail.com".',
                'email.unique' => 'This email is already used by another student.',
                'age.required' => 'The age field is required.',
                'age.integer' => 'The age must be a number.',
                'age.min' => 'The age must be at least 1.',
            ]);

            $student->update($validated);

            return response()->json([
                'message' => 'Student updated successfully',
                'data' => $student,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Student not found',
                'data' => null,
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);

            $student->delete();

            return response()->json([
                'message' => 'Student deleted',
                'data' => [
                    'id' => $id
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Student not found',
                'data' => null,
            ], 404);
        }
    }
}
