<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FaceVerificationService
{
    public function verify(User $user, string $candidateImageBinary): array
    {
        if (! config('absensi.face_verification.enabled')) {
            return $this->skipped('Verifikasi wajah belum diaktifkan.');
        }

        if (! $user->foto_profil || ! Storage::disk('public')->exists($user->foto_profil)) {
            return $this->skipped('Foto profil belum tersedia sebagai pembanding.');
        }

        $endpoint = (string) config('absensi.face_verification.endpoint');
        if ($endpoint === '' || str_contains($endpoint, 'example.com') || str_contains($endpoint, 'endpoint-face-recognition-kamu')) {
            return $this->unavailable('Endpoint verifikasi wajah belum dikonfigurasi.');
        }

        $referenceImageBinary = Storage::disk('public')->get($user->foto_profil);
        $candidateImageBinary = $this->normalizeImageBinary($candidateImageBinary);
        if ($candidateImageBinary === null) {
            return $this->unavailable('Format foto absensi tidak valid.');
        }

        $threshold = (float) config('absensi.face_verification.threshold', 0.75);

        try {
            $request = Http::timeout((int) config('absensi.face_verification.timeout', 8))
                ->acceptJson()
                ->asMultipart();

            $token = (string) config('absensi.face_verification.token');
            if ($token !== '') {
                $request = $request->withToken($token);
            }

            $response = $request
                ->attach('reference_image', $referenceImageBinary, basename($user->foto_profil))
                ->attach('candidate_image', $candidateImageBinary, 'absensi.jpg')
                ->post($endpoint, [
                    'threshold' => $threshold,
                    'user_id' => $user->id_user,
                ]);
        } catch (\Throwable $exception) {
            return $this->unavailable('Layanan verifikasi wajah tidak bisa dihubungi.');
        }

        if (! $response->successful()) {
            return $this->unavailable('Layanan verifikasi wajah mengembalikan error.');
        }

        $payload = $response->json();
        if (! is_array($payload)) {
            return $this->unavailable('Respons verifikasi wajah tidak valid.');
        }

        $confidence = $this->confidence($payload);
        $matched = $this->matched($payload, $confidence, $threshold);

        return [
            'status' => $matched ? 'matched' : 'mismatched',
            'confidence' => $confidence,
            'reason' => $payload['message'] ?? null,
        ];
    }

    public function shouldFailClosed(array $result): bool
    {
        if (! config('absensi.face_verification.enabled')) {
            return false;
        }

        return in_array($result['status'], ['unavailable', 'skipped'], true)
            && ! (bool) config('absensi.face_verification.fail_open', true);
    }

    private function matched(array $payload, ?float $confidence, float $threshold): bool
    {
        $serviceMatched = null;

        if (array_key_exists('match', $payload)) {
            $serviceMatched = (bool) $payload['match'];
        } elseif (array_key_exists('verified', $payload)) {
            $serviceMatched = (bool) $payload['verified'];
        }

        if ($serviceMatched === false) {
            return false;
        }

        if ($confidence !== null) {
            return $confidence >= $threshold;
        }

        return $serviceMatched === true;
    }

    private function confidence(array $payload): ?float
    {
        foreach (['confidence', 'similarity', 'score'] as $key) {
            if (isset($payload[$key]) && is_numeric($payload[$key])) {
                $value = (float) $payload[$key];

                return $value > 1 && $value <= 100 ? $value / 100 : $value;
            }
        }

        return null;
    }

    private function normalizeImageBinary(string $image): ?string
    {
        if (! str_starts_with($image, 'data:image/')) {
            return $image;
        }

        if (! preg_match('/^data:image\/(?:jpeg|jpg|png|webp);base64,/', $image)) {
            return null;
        }

        $parts = explode(';base64,', $image, 2);
        if (count($parts) !== 2) {
            return null;
        }

        $decoded = base64_decode($parts[1], true);
        if ($decoded === false || $decoded === '') {
            return null;
        }

        return $decoded;
    }

    private function skipped(string $reason): array
    {
        return ['status' => 'skipped', 'confidence' => null, 'reason' => $reason];
    }

    private function unavailable(string $reason): array
    {
        return ['status' => 'unavailable', 'confidence' => null, 'reason' => $reason];
    }
}
