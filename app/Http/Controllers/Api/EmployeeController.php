<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Http\Resources\APIResource;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Mengambil data dari database
        $employee = Employee::with('position')->get();
        //Menampilkan responses JSON dari API Resource
        return new APIResource(true, 'Showing Employee Data', $employee);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Melakukan Pesan Validasi Nilai Input
        $messages = [
            'required' => ':Attribute must be filled.',
            'email' => 'Fill :attribute with the correct format.',
            'numeric' => 'Fill :attribute with numeric.',
            'email.unique' => 'The email address has been registered.',
            'position_id.exists' => 'The specified position does not exist.'
        ];

        // Validasi
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email|unique:employees,email',
            'age' => 'required|numeric',
            'position_id' => 'required|integer|exists:positions,id',
        ], $messages);

        // Check Response Validasi
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Handle file upload
        $file = $request->file('cv');
        if ($file != null) {
            $originalFilename = $file->getClientOriginalName();
            $encryptedFilename = $file->hashName();
            $file->store('public/files');
        }

        // Create new employee record
        $employee = new Employee();
        $employee->firstname = $request->input('firstName');
        $employee->lastname = $request->input('lastName');
        $employee->email = $request->input('email');
        $employee->age = $request->input('age');
        $employee->position_id = $request->input('position_id');

        if ($file != null) {
            $employee->original_filename = $originalFilename;
            $employee->encrypted_filename = $encryptedFilename;
        }

        $employee->save();

        return new APIResource(true, 'Employee data added successfully', $employee);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Mencari data employee dari id dengan relasi position
        $employee = Employee::with('position')->find($id);

        // Periksa apakah employee ditemukan
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }

        // Mengeluarkan Response Hasil
        return new APIResource(true, 'Showing Employee Data', $employee);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Melakukan Pesan Validasi Nilai Input
        $messages = [
            'required' => ':Attribute must be filled.',
            'email' => 'Fill :attribute with the correct format.',
            'numeric' => 'Fill :attribute with numeric.',
            'email.unique' => 'The email address has been registered.',
            'position_id.exists' => 'The specified position does not exist.'
        ];

        // Validasi
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email|unique:employees,email,' . $id, // Ignore unique validation for the current employee's email
            'age' => 'required|numeric',
            'position_id' => 'required|integer|exists:positions,id',
        ], $messages);

        // Check Response Validasi
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find the existing employee record
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }

        // Handle file upload
        $file = $request->file('cv');
        if ($file != null) {
            $originalFilename = $file->getClientOriginalName();
            $encryptedFilename = $file->hashName();
            $file->store('public/files');
        }

        // Update employee record
        $employee->firstname = $request->input('firstName');
        $employee->lastname = $request->input('lastName');
        $employee->email = $request->input('email');
        $employee->age = $request->input('age');
        $employee->position_id = $request->input('position_id');

        if ($file != null) {
            $employee->original_filename = $originalFilename;
            $employee->encrypted_filename = $encryptedFilename;
        }

        $employee->save();

        return new APIResource(true, 'Employee data updated successfully', $employee);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Mencari data employee dari id dengan relasi position
        $employee = Employee::find($id);

        // Periksa apakah employee ditemukan
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }

        $employee->delete();

        return new APIResource(true, 'Employee data deleted successfully', $employee);
      }

}
