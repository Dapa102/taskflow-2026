<?php

use App\Models\Task;
use App\Models\User;
use App\Notifications\DeadlineReminderNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;
    $this->headers = ['Authorization' => "Bearer {$this->token}"];
});

describe('Notification API', function () {
    it('lists notifications for authenticated user', function () {
        Notification::send($this->user, new DeadlineReminderNotification(
            Task::factory()->create(['user_id' => $this->user->id, 'deadline' => now()->addDay()]),
            'tomorrow'
        ));

        $response = $this->getJson('/api/notifications', $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('unread_count', 1);
    });

    it('marks a notification as read', function () {
        Notification::send($this->user, new DeadlineReminderNotification(
            Task::factory()->create(['user_id' => $this->user->id, 'deadline' => now()->addDay()]),
            'tomorrow'
        ));

        $notificationId = $this->user->notifications->first()->id;

        $response = $this->postJson("/api/notifications/{$notificationId}/read", [], $this->headers);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertTrue($this->user->fresh()->notifications->first()->read());
    });

    it('marks all notifications as read', function () {
        $task1 = Task::factory()->create(['user_id' => $this->user->id, 'deadline' => now()->addDay()]);
        $task2 = Task::factory()->create(['user_id' => $this->user->id, 'deadline' => now()->addDay()]);

        Notification::send($this->user, new DeadlineReminderNotification($task1, 'tomorrow'));
        Notification::send($this->user, new DeadlineReminderNotification($task2, 'overdue'));

        $response = $this->postJson('/api/notifications/read-all', [], $this->headers);

        $response->assertStatus(200);
        expect($this->user->fresh()->unreadNotifications->count())->toBe(0);
    });

    it('deletes a notification', function () {
        Notification::send($this->user, new DeadlineReminderNotification(
            Task::factory()->create(['user_id' => $this->user->id, 'deadline' => now()->addDay()]),
            'tomorrow'
        ));

        $notificationId = $this->user->notifications->first()->id;

        $response = $this->deleteJson("/api/notifications/{$notificationId}", [], $this->headers);

        $response->assertStatus(200);
        expect($this->user->fresh()->notifications->count())->toBe(0);
    });
});

describe('Deadline Reminder Command', function () {
    it('sends reminder for tasks due tomorrow', function () {
        Notification::fake();

        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'deadline' => now()->addDay()->toDateString(),
            'status' => 'todo',
        ]);

        $this->artisan('reminders:deadline')
            ->assertSuccessful();

        Notification::assertSentTo($this->user, DeadlineReminderNotification::class, function ($notification) {
            return $notification->reminderType === 'tomorrow';
        });
    });

    it('sends overdue reminder for past deadline tasks', function () {
        Notification::fake();

        Task::factory()->create([
            'user_id' => $this->user->id,
            'deadline' => now()->subDay()->toDateString(),
            'status' => 'todo',
        ]);

        $this->artisan('reminders:deadline')
            ->assertSuccessful();

        Notification::assertSentTo($this->user, DeadlineReminderNotification::class, function ($notification) {
            return $notification->reminderType === 'overdue';
        });
    });

    it('does not send reminder for done tasks', function () {
        Notification::fake();

        Task::factory()->create([
            'user_id' => $this->user->id,
            'deadline' => now()->addDay()->toDateString(),
            'status' => 'done',
        ]);

        $this->artisan('reminders:deadline')
            ->assertSuccessful();

        Notification::assertNothingSentTo($this->user);
    });
});

describe('Notification Data', function () {
    it('contains correct task info in toArray', function () {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Important Task',
            'deadline' => '2026-07-01',
        ]);

        $notification = new DeadlineReminderNotification($task, 'tomorrow');
        $data = $notification->toArray($this->user);

        expect($data['task_id'])->toBe($task->id);
        expect($data['task_title'])->toBe('Important Task');
        expect($data['deadline'])->toBe('2026-07-01');
        expect($data['reminder_type'])->toBe('tomorrow');
    });
});
