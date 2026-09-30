<?php

namespace Tests\Feature\Reports;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_the_personnel_manager_can_access_reports(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrateur]);
        $responsable = User::factory()->create(['role' => UserRole::ResponsablePersonnel]);

        $this->actingAs($administrator)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($responsable)->get(route('reports.index'))->assertOk();
    }

    public function test_the_administrator_dashboard_does_not_show_reports(): void
    {
        $administrator = User::factory()->create(['role' => UserRole::Administrateur]);

        $this->actingAs($administrator)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Rapports');
    }
}
