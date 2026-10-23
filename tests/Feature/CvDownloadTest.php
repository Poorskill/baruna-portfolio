<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CvDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_cv_download_returns_404_when_no_cv_is_configured(): void
    {
        $response = $this->get('/cv');

        $response->assertStatus(404);
    }

    public function test_cv_download_returns_404_when_file_does_not_exist_on_disk(): void
    {
        Storage::fake('public');
        SiteSetting::set('cv_path', 'documents/nonexistent.pdf', 'identity');

        $response = $this->get('/cv');

        $response->assertStatus(404);
    }

    public function test_cv_download_serves_pdf_with_proper_filename_when_available(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('Baruna-CV.pdf', 500, 'application/pdf');
        $path = $file->store('documents', 'public');

        SiteSetting::set('cv_path', $path, 'identity');

        $response = $this->get('/cv');

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=Baruna-Dwi-Cahya-CV.pdf');
    }

    public function test_home_page_indicates_cv_availability(): void
    {
        Storage::fake('public');

        // Case 1: No CV
        $resWithoutCv = $this->get('/');
        $resWithoutCv->assertStatus(200);
        $resWithoutCv->assertInertia(fn ($page) => $page
            ->component('Public/Home')
            ->where('hasCv', false)
        );

        // Case 2: Upload valid CV
        $file = UploadedFile::fake()->create('Baruna-CV.pdf', 500, 'application/pdf');
        $path = $file->store('documents', 'public');
        SiteSetting::set('cv_path', $path, 'identity');

        $resWithCv = $this->get('/');
        $resWithCv->assertStatus(200);
        $resWithCv->assertInertia(fn ($page) => $page
            ->component('Public/Home')
            ->where('hasCv', true)
        );
    }

    public function test_admin_can_upload_and_replace_cv(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        // 1st Upload
        $cv1 = UploadedFile::fake()->create('cv-v1.pdf', 300, 'application/pdf');
        $res1 = $this->actingAs($admin)->put('/admin/content/profile', [
            'about_short' => 'Short bio',
            'about_long' => 'Long bio',
            'cv' => $cv1,
        ]);
        $res1->assertSessionHas('success');

        $path1 = SiteSetting::get('cv_path');
        $this->assertNotNull($path1);
        Storage::disk('public')->assertExists($path1);

        // 2nd Upload (Replaces old CV)
        $cv2 = UploadedFile::fake()->create('cv-v2.pdf', 400, 'application/pdf');
        $res2 = $this->actingAs($admin)->put('/admin/content/profile', [
            'about_short' => 'Short bio updated',
            'about_long' => 'Long bio updated',
            'cv' => $cv2,
        ]);
        $res2->assertSessionHas('success');

        $path2 = SiteSetting::get('cv_path');
        $this->assertNotEquals($path1, $path2);
        Storage::disk('public')->assertMissing($path1);
        Storage::disk('public')->assertExists($path2);
    }

    public function test_admin_can_upload_and_replace_profile_photo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        // 1st photo upload
        $photo1 = UploadedFile::fake()->image('baruna-portrait.jpg', 600, 600);
        $res1 = $this->actingAs($admin)->put('/admin/content/profile', [
            'about_short' => 'Developer bio',
            'photo' => $photo1,
        ]);
        $res1->assertSessionHas('success');

        $path1 = SiteSetting::get('photo_path');
        $this->assertNotNull($path1);
        Storage::disk('public')->assertExists($path1);

        // 2nd photo upload replaces 1st
        $photo2 = UploadedFile::fake()->image('baruna-portrait-new.png', 600, 600);
        $res2 = $this->actingAs($admin)->put('/admin/content/profile', [
            'about_short' => 'Developer bio updated',
            'photo' => $photo2,
        ]);
        $res2->assertSessionHas('success');

        $path2 = SiteSetting::get('photo_path');
        $this->assertNotEquals($path1, $path2);
        Storage::disk('public')->assertMissing($path1);
        Storage::disk('public')->assertExists($path2);
    }

    public function test_admin_can_remove_profile_photo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $photo = UploadedFile::fake()->image('photo.jpg', 400, 400);
        $this->actingAs($admin)->put('/admin/content/profile', [
            'photo' => $photo,
        ]);

        $path = SiteSetting::get('photo_path');
        Storage::disk('public')->assertExists($path);

        // Remove photo
        $resRemove = $this->actingAs($admin)->put('/admin/content/profile', [
            'remove_photo' => 1,
        ]);
        $resRemove->assertSessionHas('success');

        $this->assertEmpty(SiteSetting::get('photo_path'));
        Storage::disk('public')->assertMissing($path);
    }
}
