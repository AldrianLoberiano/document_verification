<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_returns_the_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_login_alias_redirects_to_admin_login(): void
    {
        $response = $this->get('/login');

        $response->assertRedirect('/admin/login');
    }

    public function test_check_alias_redirects_to_verification_page(): void
    {
        $response = $this->get('/check');

        $response->assertRedirect('/verify/check');
    }
}
