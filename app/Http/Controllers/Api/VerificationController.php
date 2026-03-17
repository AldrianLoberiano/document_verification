<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\VerificationAttempt;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Request $request, string $code)
    {
        $document = Document::query()->where('verification_code', strtoupper($code))->first();

        if (! $document) {
            $this->logAttempt($request, null, $code, 'failed');

            return response()->json([
                'valid' => false,
                'message' => 'Document not found. This code is not registered in the system.',
            ], 404);
        }

        if ($document->status === 'revoked') {
            $this->logAttempt($request, $document, $code, 'revoked');

            return response()->json([
                'valid' => false,
                'message' => 'This document has been revoked and is no longer valid.',
                'document' => $this->publicDocumentData($document),
            ], 410);
        }

        $this->logAttempt($request, $document, $code, 'success');

        return response()->json([
            'valid' => true,
            'message' => 'Document is authentic.',
            'document' => $this->publicDocumentData($document),
        ]);
    }

    private function publicDocumentData(Document $document): array
    {
        return [
            'title' => $document->title,
            'document_type' => $document->document_type,
            'document_number' => $document->document_number,
            'recipient_name' => $document->recipient_name,
            'issued_at' => optional($document->issued_at)->toDateString(),
            'expires_at' => optional($document->expires_at)->toDateString(),
            'status' => $document->status,
            'verification_code' => $document->verification_code,
            'verification_url' => url('/verify/' . $document->verification_code),
        ];
    }

    private function logAttempt(Request $request, ?Document $document, string $code, string $status): void
    {
        VerificationAttempt::query()->create([
            'document_id' => $document?->id,
            'verification_code' => strtoupper($code),
            'status' => $status,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'attempted_at' => now(),
        ]);
    }
}
