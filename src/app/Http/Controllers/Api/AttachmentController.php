<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function index(Task $task): JsonResponse
    {
        Gate::authorize('view', $task);

        $attachments = $task->attachments()->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $attachments,
        ]);
    }

    public function store(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('update', $task);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,docx,doc,xlsx,xls'],
        ]);

        $file = $validated['file'];
        $path = $file->store("attachments/{$task->id}", 'public');

        $attachment = $task->attachments()->create([
            'user_id' => $request->user()->id,
            'filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $attachment,
        ], 201);
    }

    public function destroy(Attachment $attachment): JsonResponse
    {
        Gate::authorize('update', $attachment->task);

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Attachment deleted',
        ]);
    }
}
