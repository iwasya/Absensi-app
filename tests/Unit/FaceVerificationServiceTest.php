<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\FaceVerificationService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FaceVerificationServiceTest extends TestCase
{
    public function test_it_skips_when_face_verification_is_disabled(): void
    {
        config(['absensi.face_verification.enabled' => false]);

        $result = app(FaceVerificationService::class)->verify(new User(), 'candidate');

        $this->assertSame('skipped', $result['status']);
    }

    public function test_it_marks_mismatch_from_face_verification_response(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('profil/user.jpg', 'reference');

        config([
            'absensi.face_verification.enabled' => true,
            'absensi.face_verification.endpoint' => 'https://face.test/verify',
            'absensi.face_verification.threshold' => 0.75,
            'absensi.face_verification.timeout' => 8,
            'absensi.face_verification.token' => null,
        ]);

        Http::fake([
            'face.test/verify' => Http::response([
                'match' => false,
                'confidence' => 0.41,
            ]),
        ]);

        $user = new User([
            'foto_profil' => 'profil/user.jpg',
        ]);
        $user->id_user = 12;

        $result = app(FaceVerificationService::class)->verify($user, 'candidate');

        $this->assertSame('mismatched', $result['status']);
        $this->assertSame(0.41, $result['confidence']);
    }

    public function test_it_treats_placeholder_endpoint_as_unavailable(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('profil/user.jpg', 'reference');

        config([
            'absensi.face_verification.enabled' => true,
            'absensi.face_verification.endpoint' => 'https://endpoint-face-recognition-kamu/verify-face',
        ]);

        Http::fake();

        $user = new User([
            'foto_profil' => 'profil/user.jpg',
        ]);

        $result = app(FaceVerificationService::class)->verify($user, 'candidate');

        $this->assertSame('unavailable', $result['status']);
        Http::assertNothingSent();
    }
}
