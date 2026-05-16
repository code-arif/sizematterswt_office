<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class NoteController extends Controller
{
    use ApiResponse;

    /**
     * List all notes for the authenticated user
     */
    public function index(): JsonResponse
    {
        $notes = auth('api')->user()->notes()->latest()->get();

        return $this->success('Notes retrieved successfully.', [
            'notes' => NoteResource::collection($notes)
        ]);
    }

    /**
     * Store a new note
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title'   => 'nullable|string|max:255',
            'content' => 'required|string',
            'color'   => 'nullable|string|max:50',
        ]);

        $note = auth('api')->user()->notes()->create([
            'title'   => $request->title,
            'content' => $request->content,
            'color'   => $request->get('color', 'green'),
        ]);

        return $this->success('Note created successfully.', [
            'note' => new NoteResource($note)
        ], 201);
    }

    /**
     * Show a single note
     */
    public function show(Note $note): JsonResponse
    {
        if ($note->user_id !== auth('api')->id()) {
            return $this->error('Unauthorized.', null, 403);
        }

        return $this->success('Note retrieved successfully.', [
            'note' => new NoteResource($note)
        ]);
    }

    /**
     * Update a note
     */
    public function update(Request $request, Note $note): JsonResponse
    {
        if ($note->user_id !== auth('api')->id()) {
            return $this->error('Unauthorized.', null, 403);
        }

        $request->validate([
            'title'   => 'nullable|string|max:255',
            'content' => 'required|string',
            'color'   => 'nullable|string|max:50',
        ]);

        $note->update($request->only(['title', 'content', 'color']));

        return $this->success('Note updated successfully.', [
            'note' => new NoteResource($note)
        ]);
    }

    /**
     * Delete a note
     */
    public function destroy(Note $note): JsonResponse
    {
        if ($note->user_id !== auth('api')->id()) {
            return $this->error('Unauthorized.', null, 403);
        }

        $note->delete();

        return $this->success('Note deleted successfully.');
    }

    /**
     * Auto-save note (create or update)
     * Used for continuous saving (e.g., on debounced key presses)
     */
    public function autoSave(Request $request): JsonResponse
    {
        $request->validate([
            'note_id'   => 'nullable|exists:notes,id',
            'title'     => 'nullable|string|max:255',
            'content'   => 'required|string',
            'color'     => 'nullable|string|max:50',
        ]);

        $userId = auth('api')->id();
        $noteId = $request->note_id;

        if ($noteId) {
            // Update existing note
            $note = Note::findOrFail($noteId);
            if ($note->user_id !== $userId) {
                return $this->error('Unauthorized.', null, 403);
            }
            $note->update($request->only(['title', 'content', 'color']));
        } else {
            // Create new note
            $note = auth('api')->user()->notes()->create([
                'title'   => $request->title,
                'content' => $request->content,
                'color'   => $request->get('color', 'green'),
            ]);
        }

        return $this->success('Note saved successfully.', [
            'note' => new NoteResource($note)
        ], $noteId ? 200 : 201);
    }
}
