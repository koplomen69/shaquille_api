<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    public function index()
    {
        return Position::all();
    }

    public function store(Request $request)
{
    // Melakukan Pesan Validasi Nilai Input
    $messages = [
        'required' => ':Attribute must be filled.',
        'unique' => 'The :attribute has already been taken.',
    ];

    // Validasi
    $validator = Validator::make($request->all(), [
        'code' => 'required|unique:positions',
        'name' => 'required|unique:positions',
        'description' => 'required',
    ], $messages);

    // Check Response Validasi
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Membuat posisi baru
    $position = new Position();
    $position->code = $request->input('code');
    $position->name = $request->input('name');
    $position->description = $request->input('description');

    $position->save();

    return response()->json(['message' => 'Position added successfully'], 201);
}

    public function show($id)
    {
        return Position::findOrFail($id);
    }

    public function update(Request $request, string $id)
{
    // Melakukan Pesan Validasi Nilai Input
    $messages = [
        'required' => ':Attribute must be filled.',
        'unique' => 'The :attribute has already been taken.',
        'exists' => 'The specified :attribute does not exist.'
    ];

    // Validasi
    $validator = Validator::make($request->all(), [
        'code' => 'required|unique:positions,code,' . $id,
        'name' => 'required|unique:positions,name,' . $id,
        'description' => 'required',
    ], $messages);

    // Check Response Validasi
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Mencari record posisi yang sudah ada
    $position = Position::find($id);

    // Memeriksa apakah posisi ditemukan
    if (!$position) {
        return response()->json(['success' => false, 'message' => 'Position not found'], 404);
    }

    // Memperbarui data posisi
    $position->code = $request->input('code');
    $position->name = $request->input('name');
    $position->description = $request->input('description');

    $position->save();

    return response()->json(['message' => 'Position updated successfully'], 200);
}


public function destroy(string $id)
{
    // Mencari data posisi dari id
    $position = Position::find($id);

    // Periksa apakah posisi ditemukan
    if (!$position) {
        return response()->json(['success' => false, 'message' => 'Position not found'], 404);
    }

    // Hapus posisi
    $position->delete();

    return response()->json(['message' => 'Position deleted successfully'], 200);
}

}
