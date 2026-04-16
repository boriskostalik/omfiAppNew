<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Issue;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    // =========================
    // VEREJNÉ STRÁNKY
    // =========================

    public function test_homepage_is_accessible()
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_archive_is_accessible()
    {
        $this->get('/archive')->assertStatus(200);
    }

    public function test_search_is_accessible()
    {
        $this->get('/search')->assertStatus(200);
    }

    public function test_about_is_accessible()
    {
        $this->get('/about')->assertStatus(200);
    }

    // =========================
    // AUTENTIFIKÁCIA
    // =========================

    public function test_login_page_is_accessible()
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ])->assertRedirect('/dashboard');
    }

    public function test_user_cannot_login_with_wrong_password()
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/');
    }

    // =========================
    // OCHRANA DASHBOARDU
    // =========================

    public function test_unauthenticated_user_cannot_access_dashboard()
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_unauthenticated_user_cannot_access_publications_dashboard()
    {
        $this->get('/dashboard/publications')->assertRedirect('/login');
    }

    public function test_unauthenticated_user_cannot_access_authors_dashboard()
    {
        $this->get('/dashboard/authors')->assertRedirect('/login');
    }

    public function test_unauthenticated_user_cannot_access_issues_dashboard()
    {
        $this->get('/dashboard/issues')->assertRedirect('/login');
    }

    // =========================
    // AUTORIZÁCIA ROLÍ
    // =========================

    public function test_editor_cannot_access_user_management()
    {
        $editor = User::factory()->create(['role' => 'editor']);

        $this->actingAs($editor)
            ->get('/dashboard/users')
            ->assertStatus(401);
    }

    public function test_admin_can_access_user_management()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/dashboard/users')
            ->assertStatus(200);
    }

    // =========================
    // SPRÁVA PUBLIKÁCIÍ
    // =========================

    public function test_authenticated_user_can_access_publications_dashboard()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard/publications')
            ->assertStatus(200);
    }

    public function test_publication_requires_title()
    {
        $user  = User::factory()->create();
        $issue = Issue::factory()->create();

        $this->actingAs($user)
            ->post('/dashboard/publications', [
                'title'     => '',
                'issue_id'  => $issue->id,
                'type'      => 'Article',
                'firstpage' => '1',
                'lastpage'  => '10',
                'authors'   => [],
            ])
            ->assertSessionHasErrors('title');
    }

    public function test_publication_requires_issue()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/dashboard/publications', [
                'title'     => 'Test publikácia',
                'issue_id'  => null,
                'type'      => 'Article',
                'firstpage' => '1',
                'lastpage'  => '10',
            ])
            ->assertSessionHasErrors('issue_id');
    }

    public function test_authenticated_user_can_create_publication()
    {
        $user  = User::factory()->create();
        $issue = Issue::factory()->create();

        $this->actingAs($user)
            ->post('/dashboard/publications', [
                'title'     => 'Nová testovacia publikácia',
                'issue_id'  => $issue->id,
                'type'      => 'Article',
                'firstpage' => '1',
                'lastpage'  => '10',
                'authors'   => [],
            ])
            ->assertRedirect('/dashboard/publications');

        $this->assertDatabaseHas('publications', ['title' => 'Nová testovacia publikácia']);
    }

    public function test_authenticated_user_can_update_publication()
    {
        $user        = User::factory()->create();
        $issue       = Issue::factory()->create();
        $publication = Publication::factory()->create(['issue_id' => $issue->id]);

        $this->actingAs($user)
            ->put("/dashboard/publications/{$publication->id}", [
                'title'     => 'Upravený názov publikácie',
                'issue_id'  => $issue->id,
                'type'      => 'Article',
                'firstpage' => '1',
                'lastpage'  => '10',
                'authors'   => [],
            ])
            ->assertRedirect('/dashboard/publications');

        $this->assertDatabaseHas('publications', ['title' => 'Upravený názov publikácie']);
    }

    public function test_authenticated_user_can_delete_publication()
    {
        $user        = User::factory()->create();
        $issue       = Issue::factory()->create();
        $publication = Publication::factory()->create(['issue_id' => $issue->id]);

        $this->actingAs($user)
            ->delete("/dashboard/publications/{$publication->id}")
            ->assertRedirect('/dashboard/publications');

        $this->assertDatabaseMissing('publications', ['id' => $publication->id]);
    }

    // =========================
    // SPRÁVA AUTOROV
    // =========================

    public function test_authenticated_user_can_access_authors_dashboard()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard/authors')
            ->assertStatus(200);
    }

    public function test_author_requires_surname()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/dashboard/authors', [
                'surname'   => '',
                'firstname' => 'Ján',
            ])
            ->assertSessionHasErrors('surname');
    }

    public function test_authenticated_user_can_create_author()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/dashboard/authors', [
                'surname'   => 'Novák',
                'firstname' => 'Ján',
            ])
            ->assertRedirect('/dashboard/authors');

        $this->assertDatabaseHas('authors', ['surname' => 'Novák', 'firstname' => 'Ján']);
    }

    public function test_authenticated_user_can_update_author()
    {
        $user   = User::factory()->create();
        $author = Author::factory()->create();

        $this->actingAs($user)
            ->put("/dashboard/authors/{$author->id}", [
                'surname'   => 'Upravený',
                'firstname' => 'Autor',
            ])
            ->assertRedirect('/dashboard/authors');

        $this->assertDatabaseHas('authors', ['id' => $author->id, 'surname' => 'Upravený']);
    }

    public function test_authenticated_user_can_delete_author()
    {
        $user   = User::factory()->create();
        $author = Author::factory()->create();

        $this->actingAs($user)
            ->delete("/dashboard/authors/{$author->id}")
            ->assertRedirect('/dashboard/authors');

        $this->assertDatabaseMissing('authors', ['id' => $author->id]);
    }

    // =========================
    // SPRÁVA VYDANÍ
    // =========================

    public function test_authenticated_user_can_access_issues_dashboard()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard/issues')
            ->assertStatus(200);
    }

    public function test_issue_requires_year()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/dashboard/issues', [
                'year'   => '',
                'number' => '1',
            ])
            ->assertSessionHasErrors('year');
    }

    public function test_authenticated_user_can_create_issue()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/dashboard/issues', [
                'year'   => 2025,
                'number' => 1,
                'volume' => '54',
            ])
            ->assertRedirect('/dashboard/issues');

        $this->assertDatabaseHas('issues', ['year' => 2025, 'number' => 1]);
    }

    public function test_authenticated_user_can_update_issue()
    {
        $user  = User::factory()->create();
        $issue = Issue::factory()->create(['year' => 2020, 'number' => 1]);

        $this->actingAs($user)
            ->put("/dashboard/issues/{$issue->id}", [
                'year'   => 2020,
                'number' => 2,
                'volume' => '49',
            ])
            ->assertRedirect('/dashboard/issues');

        $this->assertDatabaseHas('issues', ['id' => $issue->id, 'number' => 2]);
    }

    public function test_authenticated_user_can_delete_issue()
    {
        $user  = User::factory()->create();
        $issue = Issue::factory()->create();

        $this->actingAs($user)
            ->delete("/dashboard/issues/{$issue->id}")
            ->assertRedirect('/dashboard/issues');

        $this->assertDatabaseMissing('issues', ['id' => $issue->id]);
    }

    public function test_issue_number_must_be_unique_per_year()
    {
        $user  = User::factory()->create();
        Issue::factory()->create(['year' => 2024, 'number' => 1]);

        $this->actingAs($user)
            ->post('/dashboard/issues', [
                'year'   => 2024,
                'number' => 1,
            ])
            ->assertSessionHasErrors('number');
    }
}
