<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\VerificationAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::query()
            ->with('creator:id,name,email')
            ->latest()
            ->get()
            ->map(fn(Document $document) => $this->transformDocument($document));

        return response()->json($documents);
    }

    public function show(Document $document)
    {
        $document->load('creator:id,name,email');

        return response()->json($this->transformDocument($document));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'issued_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'document_file' => ['required', 'file', 'max:10240', 'mimes:pdf,png,jpg,jpeg'],
        ]);

        $file = $request->file('document_file');
        $storedPath = $file->store('documents', 'local');
        $absolutePath = Storage::disk('local')->path($storedPath);

        $document = Document::query()->create([
            'public_id' => (string) Str::uuid(),
            'verification_code' => $this->generateVerificationCode(),
            'title' => $validated['title'],
            'document_type' => $validated['document_type'],
            'document_number' => $validated['document_number'] ?? null,
            'recipient_name' => $validated['recipient_name'] ?? null,
            'issued_at' => $validated['issued_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'status' => 'active',
            'notes' => $validated['notes'] ?? null,
            'file_original_name' => $file->getClientOriginalName(),
            'file_mime_type' => $file->getMimeType() ?? 'application/octet-stream',
            'file_size' => $file->getSize(),
            'file_path' => $storedPath,
            'file_checksum' => hash_file('sha256', $absolutePath),
            'created_by' => $request->user()->id,
        ]);

        $document->load('creator:id,name,email');

        return response()->json($this->transformDocument($document), 201);
    }

    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'document_type' => ['sometimes', 'required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'issued_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:active,revoked'],
            'document_file' => ['nullable', 'file', 'max:10240', 'mimes:pdf,png,jpg,jpeg'],
        ]);

        $document->fill($validated);

        if ($request->hasFile('document_file')) {
            $newFile = $request->file('document_file');

            if (Storage::disk('local')->exists($document->file_path)) {
                Storage::disk('local')->delete($document->file_path);
            }

            $newPath = $newFile->store('documents', 'local');
            $absolutePath = Storage::disk('local')->path($newPath);

            $document->file_original_name = $newFile->getClientOriginalName();
            $document->file_mime_type = $newFile->getMimeType() ?? 'application/octet-stream';
            $document->file_size = $newFile->getSize();
            $document->file_path = $newPath;
            $document->file_checksum = hash_file('sha256', $absolutePath);
        }

        $document->save();
        $document->load('creator:id,name,email');

        return response()->json($this->transformDocument($document));
    }

    public function revoke(Document $document)
    {
        $document->status = 'revoked';
        $document->save();

        return response()->json([
            'message' => 'Document revoked successfully.',
            'document' => $this->transformDocument($document),
        ]);
    }

    public function destroy(Document $document)
    {
        if (Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }

        $document->delete();

        return response()->json(['message' => 'Document deleted.']);
    }

    public function download(Document $document)
    {
        if (! Storage::disk('local')->exists($document->file_path)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        return response()->download(
            Storage::disk('local')->path($document->file_path),
            $document->file_original_name
        );
    }

    public function logs()
    {
        $logs = VerificationAttempt::query()
            ->with('document:id,title,verification_code,status')
            ->latest('attempted_at')
            ->limit(200)
            ->get()
            ->map(function (VerificationAttempt $log) {
                return [
                    'id' => $log->id,
                    'verification_code' => $log->verification_code,
                    'status' => $log->status,
                    'ip_address' => $log->ip_address,
                    'attempted_at' => optional($log->attempted_at)->toDateTimeString(),
                    'document' => $log->document,
                ];
            });

        return response()->json($logs);
    }

    private function transformDocument(Document $document): array
    {
        return [
            'id' => $document->id,
            'public_id' => $document->public_id,
            'verification_code' => $document->verification_code,
            'title' => $document->title,
            'document_type' => $document->document_type,
            'document_number' => $document->document_number,
            'recipient_name' => $document->recipient_name,
            'issued_at' => optional($document->issued_at)->toDateString(),
            'expires_at' => optional($document->expires_at)->toDateString(),
            'status' => $document->status,
            'notes' => $document->notes,
            'file_original_name' => $document->file_original_name,
            'file_size' => $document->file_size,
            'file_checksum' => $document->file_checksum,
            'created_at' => optional($document->created_at)->toDateTimeString(),
            'creator' => $document->creator,
            'verification_url' => url('/verify/' . $document->verification_code),
        ];
    }

    private function generateVerificationCode(): string
    {
        do {
            $code = strtoupper(Str::random(12));
        } while (Document::query()->where('verification_code', $code)->exists());

        return $code;
    }
}
