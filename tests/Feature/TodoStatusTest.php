<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoStatusTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create(['role' => 'super_admin']);
    }

    public function test_todo_defaults_to_pending_on_store(): void
    {
        $admin = $this->superAdmin();

        $res = $this->actingAs($admin)->postJson('/todo/store', [
            'task' => 'Write important test',
            'task_time' => now()->toDateString(),
        ]);

        $res->assertOk()->assertJson([
            'task' => 'Write important test',
            'is_completed' => false,
            'status' => 'pending',
            'status_label' => 'Pending',
        ]);

        $this->assertDatabaseHas('todos', [
            'task' => 'Write important test',
            'is_completed' => false,
            'user_id' => $admin->id,
        ]);
    }

    public function test_user_can_update_todo_status(): void
    {
        $admin = $this->superAdmin();
        $todo = Todo::create([
            'task' => 'Check production',
            'task_time' => now(),
            'user_id' => $admin->id,
            'is_completed' => false,
        ]);

        // Mark completed
        $res = $this->actingAs($admin)->postJson("/todo/{$todo->id}/status", [
            'status' => 'completed',
        ]);

        $res->assertOk()->assertJson([
            'success' => true,
            'is_completed' => true,
            'status' => 'completed',
            'status_label' => 'Completed',
        ]);
        $this->assertTrue($todo->fresh()->is_completed);

        // Mark pending
        $res2 = $this->actingAs($admin)->postJson("/todo/{$todo->id}/status", [
            'status' => 'pending',
        ]);

        $res2->assertOk()->assertJson([
            'success' => true,
            'is_completed' => false,
            'status' => 'pending',
            'status_label' => 'Pending',
        ]);
        $this->assertFalse($todo->fresh()->is_completed);
    }

    public function test_user_can_toggle_todo_status(): void
    {
        $admin = $this->superAdmin();
        $todo = Todo::create([
            'task' => 'Deploy update',
            'task_time' => now(),
            'user_id' => $admin->id,
            'is_completed' => false,
        ]);

        $res = $this->actingAs($admin)->postJson("/todo/{$todo->id}/toggle");
        $res->assertOk()->assertJson([
            'success' => true,
            'is_completed' => true,
            'status' => 'completed',
        ]);
        $this->assertTrue($todo->fresh()->is_completed);

        $res2 = $this->actingAs($admin)->postJson("/todo/{$todo->id}/toggle");
        $res2->assertOk()->assertJson([
            'success' => true,
            'is_completed' => false,
            'status' => 'pending',
        ]);
        $this->assertFalse($todo->fresh()->is_completed);
    }

    public function test_dashboard_renders_status_icon_only_without_text(): void
    {
        $admin = $this->superAdmin();
        Todo::create([
            'task' => 'Review PR',
            'task_time' => now(),
            'user_id' => $admin->id,
            'is_completed' => false,
        ]);

        $res = $this->actingAs($admin)->get('/dashboard');
        $res->assertOk();

        $html = $res->getContent();
        $this->assertStringContainsString('todo-status-icon', $html);
        $this->assertStringContainsString('text-danger', $html);
    }

    public function test_todo_page_renders_dropdown_menu_with_completed_and_pending(): void
    {
        $admin = $this->superAdmin();
        Todo::create([
            'task' => 'Audit logs',
            'task_time' => now(),
            'user_id' => $admin->id,
            'is_completed' => false,
        ]);

        $res = $this->actingAs($admin)->get('/todo');
        $res->assertOk();

        $html = $res->getContent();
        $this->assertStringContainsString('todo-status-dropdown-btn', $html);
        $this->assertStringContainsString('Pending', $html);
        $this->assertStringContainsString('Completed', $html);
    }
}
