<?php

namespace Tests\Feature;

use App\Models\Ordinance;
use App\Models\Resolution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegislationTest extends TestCase
{
    use RefreshDatabase;

    private function validOrdinance(array $overrides = []): array
    {
        return array_merge([
            'ordinance_number' => '2026-001',
            'title'            => 'Environment Protection Ordinance',
            'subject'          => 'Environment',
            'date_passed'      => '2026-01-15',
            'author_sponsor'   => 'Hon. Sample Person',
        ], $overrides);
    }

    public function test_guests_are_redirected_to_login_from_admin_pages(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/admin/ordinances')->assertRedirect('/login');
        $this->get('/admin/resolutions')->assertRedirect('/login');
        $this->post('/admin/ordinances', $this->validOrdinance())->assertRedirect('/login');
    }

    public function test_user_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret-pass-123')]);

        $this->post('/login', ['email' => $user->email, 'password' => 'secret-pass-123'])
            ->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);

        $this->actingAs($user)->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logged_in_user_can_create_update_and_delete_an_ordinance(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/admin/ordinances', $this->validOrdinance())
            ->assertRedirect(route('admin.ordinances.index'));
        $this->assertDatabaseHas('ordinances', ['ordinance_number' => '2026-001']);

        $ordinance = Ordinance::first();

        $this->actingAs($user)
            ->put('/admin/ordinances/' . $ordinance->id, $this->validOrdinance(['title' => 'Updated Title']))
            ->assertRedirect();
        $this->assertDatabaseHas('ordinances', ['id' => $ordinance->id, 'title' => 'Updated Title']);

        $this->actingAs($user)->delete('/admin/ordinances/' . $ordinance->id)
            ->assertRedirect(route('admin.ordinances.index'));
        $this->assertDatabaseMissing('ordinances', ['id' => $ordinance->id]);
    }

    public function test_required_fields_cannot_be_blank(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/admin/ordinances', [])
            ->assertSessionHasErrors(['ordinance_number', 'title', 'subject', 'date_passed', 'author_sponsor']);

        $this->actingAs($user)->post('/admin/resolutions', [])
            ->assertSessionHasErrors(['resolution_number', 'title', 'subject', 'date_passed', 'author_sponsor']);
    }

    public function test_ordinance_number_must_be_unique(): void
    {
        $user = User::factory()->create();
        Ordinance::create($this->validOrdinance());

        $this->actingAs($user)->post('/admin/ordinances', $this->validOrdinance(['title' => 'Another']))
            ->assertSessionHasErrors('ordinance_number');
    }

    public function test_resolution_number_must_be_unique(): void
    {
        $user = User::factory()->create();
        Resolution::create([
            'resolution_number' => 'R-1',
            'title'             => 'First',
            'subject'           => 'Health',
            'date_passed'       => '2026-01-15',
            'author_sponsor'    => 'Hon. Sample Person',
        ]);

        $this->actingAs($user)->post('/admin/resolutions', [
            'resolution_number' => 'R-1',
            'title'             => 'Second',
            'subject'           => 'Health',
            'date_passed'       => '2026-01-16',
            'author_sponsor'    => 'Hon. Sample Person',
        ])->assertSessionHasErrors('resolution_number');
    }

    public function test_date_must_be_a_valid_date(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/admin/ordinances', $this->validOrdinance(['date_passed' => 'not-a-date']))
            ->assertSessionHasErrors('date_passed');

        $this->actingAs($user)->post('/admin/ordinances', $this->validOrdinance(['date_passed' => '2026-02-30']))
            ->assertSessionHasErrors('date_passed');
    }

    public function test_admin_title_search_supports_partial_keywords(): void
    {
        $user = User::factory()->create();
        Ordinance::create($this->validOrdinance(['title' => 'Environment Protection Ordinance']));
        Ordinance::create($this->validOrdinance(['ordinance_number' => '2026-002', 'title' => 'Tourism Ordinance']));

        $this->actingAs($user)->get('/admin/ordinances?title=environ')
            ->assertSee('Environment Protection Ordinance')
            ->assertDontSee('Tourism Ordinance');
    }

    public function test_admin_can_filter_by_subject(): void
    {
        $user = User::factory()->create();
        Ordinance::create($this->validOrdinance(['title' => 'First Ordinance', 'subject' => 'Health Services']));
        Ordinance::create($this->validOrdinance(['ordinance_number' => '2026-002', 'title' => 'Second Ordinance', 'subject' => 'Tourism']));

        $this->actingAs($user)->get('/admin/ordinances?subject=health')
            ->assertSee('First Ordinance')
            ->assertDontSee('Second Ordinance');
    }

    public function test_public_can_search_both_types_by_partial_keyword(): void
    {
        Ordinance::create($this->validOrdinance(['title' => 'Agriculture Development Ordinance']));
        Resolution::create([
            'resolution_number' => 'R-9',
            'title'             => 'Agriculture Support Resolution',
            'subject'           => 'Agriculture',
            'date_passed'       => '2026-01-15',
            'author_sponsor'    => 'Hon. Sample Person',
        ]);

        $this->get('/?keyword=agricultur')
            ->assertStatus(200)
            ->assertSee('Agriculture Development Ordinance')
            ->assertSee('Agriculture Support Resolution');
    }

    public function test_public_can_view_details_but_cannot_change_records(): void
    {
        $ordinance = Ordinance::create($this->validOrdinance());

        $this->get('/ordinances/' . $ordinance->id)->assertStatus(200)->assertSee('Environment Protection Ordinance');

        // Public users are sent to the login page for every admin action.
        $this->delete('/admin/ordinances/' . $ordinance->id)->assertRedirect('/login');
        $this->assertDatabaseHas('ordinances', ['id' => $ordinance->id]);
    }
}
