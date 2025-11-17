<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * Upload a file
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'required|in:profile_photo,medical_document,prescription,report,imaging,other',
            'related_type' => 'nullable|string',
            'related_id' => 'nullable|integer',
        ]);

        $user = $request->user();
        $uploadedFile = $request->file('file');

        // Validate file type based on upload type
        $this->validateFileType($uploadedFile, $request->input('type'));

        // Generate unique filename
        $originalName = $uploadedFile->getClientOriginalName();
        $extension = $uploadedFile->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;

        // Determine storage path based on type
        $path = $this->getStoragePath($request->input('type'), $user->id);

        // Store file
        $filePath = $uploadedFile->storeAs($path, $filename, 'private');

        // Create file record
        $file = File::create([
            'user_id' => $user->id,
            'filename' => $filename,
            'original_name' => $originalName,
            'path' => $filePath,
            'mime_type' => $uploadedFile->getMimeType(),
            'size' => $uploadedFile->getSize(),
            'type' => $request->input('type'),
            'related_type' => $request->input('related_type'),
            'related_id' => $request->input('related_id'),
        ]);

        return response()->json([
            'message' => 'Fichier téléchargé avec succès',
            'file' => [
                'id' => $file->id,
                'filename' => $file->filename,
                'original_name' => $file->original_name,
                'size' => $file->size,
                'type' => $file->type,
                'url' => route('files.download', $file->id),
            ],
        ], 201);
    }

    /**
     * Upload multiple files
     */
    public function uploadMultiple(Request $request): JsonResponse
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:10240', // 10MB max per file
            'type' => 'required|in:profile_photo,medical_document,prescription,report,imaging,other',
            'related_type' => 'nullable|string',
            'related_id' => 'nullable|integer',
        ]);

        $user = $request->user();
        $uploadedFiles = [];

        foreach ($request->file('files') as $uploadedFile) {
            // Validate file type
            $this->validateFileType($uploadedFile, $request->input('type'));

            // Generate unique filename
            $originalName = $uploadedFile->getClientOriginalName();
            $extension = $uploadedFile->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $extension;

            // Determine storage path
            $path = $this->getStoragePath($request->input('type'), $user->id);

            // Store file
            $filePath = $uploadedFile->storeAs($path, $filename, 'private');

            // Create file record
            $file = File::create([
                'user_id' => $user->id,
                'filename' => $filename,
                'original_name' => $originalName,
                'path' => $filePath,
                'mime_type' => $uploadedFile->getMimeType(),
                'size' => $uploadedFile->getSize(),
                'type' => $request->input('type'),
                'related_type' => $request->input('related_type'),
                'related_id' => $request->input('related_id'),
            ]);

            $uploadedFiles[] = [
                'id' => $file->id,
                'filename' => $file->filename,
                'original_name' => $file->original_name,
                'size' => $file->size,
                'type' => $file->type,
                'url' => route('files.download', $file->id),
            ];
        }

        return response()->json([
            'message' => count($uploadedFiles) . ' fichier(s) téléchargé(s) avec succès',
            'files' => $uploadedFiles,
        ], 201);
    }

    /**
     * Download a file
     */
    public function download(Request $request, int $id)
    {
        $file = File::findOrFail($id);
        $user = $request->user();

        // Check authorization
        if (!$this->canAccessFile($user, $file)) {
            return response()->json([
                'message' => 'Accès non autorisé à ce fichier',
            ], 403);
        }

        if (!Storage::disk('private')->exists($file->path)) {
            return response()->json([
                'message' => 'Fichier introuvable',
            ], 404);
        }

        return Storage::disk('private')->download($file->path, $file->original_name);
    }

    /**
     * Get file metadata
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $file = File::findOrFail($id);
        $user = $request->user();

        // Check authorization
        if (!$this->canAccessFile($user, $file)) {
            return response()->json([
                'message' => 'Accès non autorisé à ce fichier',
            ], 403);
        }

        return response()->json([
            'id' => $file->id,
            'filename' => $file->filename,
            'original_name' => $file->original_name,
            'size' => $file->size,
            'type' => $file->type,
            'mime_type' => $file->mime_type,
            'created_at' => $file->created_at,
            'url' => route('files.download', $file->id),
        ]);
    }

    /**
     * Delete a file
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $file = File::findOrFail($id);
        $user = $request->user();

        // Only file owner or admin can delete
        if ($file->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'message' => 'Non autorisé à supprimer ce fichier',
            ], 403);
        }

        // Delete from storage
        if (Storage::disk('private')->exists($file->path)) {
            Storage::disk('private')->delete($file->path);
        }

        // Delete record
        $file->delete();

        return response()->json([
            'message' => 'Fichier supprimé avec succès',
        ]);
    }

    /**
     * Get user's files
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $type = $request->query('type');

        $query = File::where('user_id', $user->id);

        if ($type) {
            $query->where('type', $type);
        }

        $files = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'files' => $files->map(fn($file) => [
                'id' => $file->id,
                'filename' => $file->filename,
                'original_name' => $file->original_name,
                'size' => $file->size,
                'type' => $file->type,
                'mime_type' => $file->mime_type,
                'created_at' => $file->created_at,
                'url' => route('files.download', $file->id),
            ]),
        ]);
    }

    /**
     * Validate file type based on upload type
     */
    private function validateFileType($file, string $type): void
    {
        $mimeType = $file->getMimeType();

        $allowedTypes = [
            'profile_photo' => ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'],
            'medical_document' => ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'],
            'prescription' => ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'],
            'report' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'imaging' => ['image/jpeg', 'image/png', 'image/jpg', 'application/dicom'],
            'other' => ['*'],
        ];

        $allowed = $allowedTypes[$type] ?? ['*'];

        if (!in_array('*', $allowed) && !in_array($mimeType, $allowed)) {
            abort(422, 'Type de fichier non autorisé pour cette catégorie');
        }
    }

    /**
     * Get storage path based on type and user ID
     */
    private function getStoragePath(string $type, int $userId): string
    {
        $basePath = "users/{$userId}";

        return match ($type) {
            'profile_photo' => "{$basePath}/photos",
            'medical_document' => "{$basePath}/medical",
            'prescription' => "{$basePath}/prescriptions",
            'report' => "{$basePath}/reports",
            'imaging' => "{$basePath}/imaging",
            default => "{$basePath}/files",
        };
    }

    /**
     * Check if user can access file
     */
    private function canAccessFile($user, File $file): bool
    {
        // Admin can access all files
        if ($user->role === 'admin') {
            return true;
        }

        // Owner can access their files
        if ($file->user_id === $user->id) {
            return true;
        }

        // Medecin can access patient files if there's an active consent
        if ($user->role === 'medecin' && $file->user->role === 'patient') {
            $consent = \App\Models\MedicalConsent::where('patient_id', $file->user->patient->id)
                ->where('medecin_id', $user->medecin->id)
                ->where('status', 'active')
                ->exists();

            return $consent;
        }

        return false;
    }
}
