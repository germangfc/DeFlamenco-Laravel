<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImagenController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $customName = 'image_' . time() . '.' . $image->getClientOriginalExtension();

            $image->storeAs('images', $customName, 'public');

            return response()->json([
                'message' => 'Imagen subida correctamente',
                'path' => $customName
            ]);
        }

        return response()->json(['message' => 'No se ha enviado ningún archivo'], 400);
    }

    public function deleteImage($filePath)
    {
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
            return response()->json(['message' => 'Imagen eliminada correctamente']);
        }

        return response()->json(['message' => 'Archivo no encontrado'], 404);
    }

    public function showImage($fileName)
    {
        $filePath = "images/" . $fileName;

        if (Storage::disk('public')->exists($filePath)) {
            $fileContent = Storage::disk('public')->get($filePath);
            $mimeType = Storage::disk('public')->mimeType($filePath);

            return response($fileContent, 200)->header('Content-Type', $mimeType);
        }

        return response()->json(['message' => 'Archivo no encontrado'], 404);
    }
}
