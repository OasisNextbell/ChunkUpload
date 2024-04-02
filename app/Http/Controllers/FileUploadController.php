<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{

    public function uploadChunk(Request $request)
    {
        $file = $request->file('file');
        $chunkIndex = $request->input('dzchunkindex');
        $chunkTotal = $request->input('dztotalchunkcount');

        $tempDirectory = "temp/{$file->getClientOriginalName()}";

        $file->storeAs($tempDirectory, "{$chunkIndex}");

        if ($chunkIndex == $chunkTotal - 1) {
            $uniqueId = uniqid();
            $filePath = "uploads/{$uniqueId}.{$file->getClientOriginalExtension()}";
            Storage::disk('local')->move("{$tempDirectory}", $filePath);
            $fileModel = new File();
            $fileModel->unique_id = $uniqueId;
            $fileModel->filename = $file->getClientOriginalName();
            $fileModel->path = $filePath;
            $fileModel->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => true]);
    }


}
