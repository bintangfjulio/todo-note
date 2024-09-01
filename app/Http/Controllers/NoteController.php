<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::where('is_done', false)
            ->orderBy('order', 'asc')
            ->paginate(10);

        return view('index', compact('notes'));
    }

    public function store(Request $request)
    {
        $filePath = null;
        $attachmentName = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $filePath = $file->store('attachments', 'public');
        }

        $scheduledAt = null;
        if ($request->has('scheduled_at')) {
            $scheduledAt = $request->input('scheduled_at');
        }

        Note::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'scheduled_at' => $scheduledAt,
            'attachment' => $filePath,
            'attachment_name' => $attachmentName,
        ]);

        return response()->json(['message' => 'Note created successfully!'], 200);
    }

    public function update(Request $request, string $id)
    {
        $note = Note::findOrFail($id);

        $filePath = $note->attachment;
        $attachmentName = $note->attachment_name;

        if ($request->hasFile('attachment')) {
            if ($filePath) {
                $oldPath = storage_path('app/public/' . $note->attachment);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $filePath = $file->store('attachments', 'public');
        }

        $scheduledAt = null;
        if ($request->has('scheduled_at')) {
            $scheduledAt = $request->input('scheduled_at');
        }

        $note->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'scheduled_at' => $scheduledAt,
            'attachment' => $filePath,
            'attachment_name' => $attachmentName,
        ]);

        return response()->json(['message' => 'Note updated successfully!'], 200);
    }

    public function destroy(string $id)
    {
        $note = Note::findOrFail($id);

        $filePath = storage_path('app/public/' . $note->attachment);

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $note->delete();

        return response()->json(['message' => 'Note deleted successfully!'], 200);
    }

    public function complete()
    {
        $notes = Note::where('is_done', true)
            ->orderBy('completed_at', 'desc')
            ->paginate(10);

        return view('complete', compact('notes'));
    }
}
