<?php

use App\Models\Attachment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;
    $this->headers = ['Authorization' => "Bearer {$this->token}"];
    $this->task = Task::factory()->create(['user_id' => $this->user->id]);

    Storage::fake('public');
});

describe('Attachment Upload', function () {
    it('uploads a file to a task', function () {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->postJson("/api/tasks/{$this->task->id}/attachments", [
            'file' => $file,
        ], $this->headers);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.filename', 'document.pdf');

        Storage::disk('public')->assertExists($response->json('data.file_path'));
    });

    it('rejects upload without file', function () {
        $response = $this->postJson("/api/tasks/{$this->task->id}/attachments", [], $this->headers);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('file');
    });

    it('rejects file larger than 5MB', function () {
        $file = UploadedFile::fake()->create('large.pdf', 6000, 'application/pdf');

        $response = $this->postJson("/api/tasks/{$this->task->id}/attachments", [
            'file' => $file,
        ], $this->headers);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('file');
    });

    it('rejects disallowed file types', function () {
        $file = UploadedFile::fake()->create('script.exe', 100, 'application/x-msdownload');

        $response = $this->postJson("/api/tasks/{$this->task->id}/attachments", [
            'file' => $file,
        ], $this->headers);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('file');
    });

    it('accepts allowed file types', function () {
        foreach (['photo.jpg', 'image.png', 'doc.pdf', 'sheet.xlsx'] as $filename) {
            $file = UploadedFile::fake()->create($filename, 100);

            $response = $this->postJson("/api/tasks/{$this->task->id}/attachments", [
                'file' => $file,
            ], $this->headers);

            $response->assertStatus(201);
        }
    });
});

describe('Attachment List', function () {
    it('lists attachments for a task', function () {
        Attachment::factory()->count(3)->create(['task_id' => $this->task->id]);

        $response = $this->getJson("/api/tasks/{$this->task->id}/attachments", $this->headers);

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    });

    it('returns 403 when listing another user task attachments', function () {
        $otherUser = User::factory()->create();
        $otherTask = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/tasks/{$otherTask->id}/attachments", $this->headers);

        $response->assertStatus(403);
    });
});

describe('Attachment Delete', function () {
    it('deletes an attachment and its file', function () {
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');
        $path = $file->store("attachments/{$this->task->id}", 'public');

        $attachment = Attachment::create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'filename' => 'test.pdf',
            'file_path' => $path,
            'file_size' => 100000,
            'mime_type' => 'application/pdf',
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->deleteJson("/api/attachments/{$attachment->id}", [], $this->headers);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
        Storage::disk('public')->assertMissing($path);
    });
});

describe('Attachment Model', function () {
    it('calculates human readable file size', function () {
        $attachment = new Attachment(['file_size' => 1048576]);
        expect($attachment->human_file_size)->toBe('1 MB');

        $attachment = new Attachment(['file_size' => 1024]);
        expect($attachment->human_file_size)->toBe('1 KB');

        $attachment = new Attachment(['file_size' => 500]);
        expect($attachment->human_file_size)->toBe('500 B');
    });
});
