<?php

namespace Tests\Functional;

use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    public function test_the_login_page_is_available_and_contains_the_login_form(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk()
            ->assertSee('PointageApp')
            ->assertSee('Connexion')
            ->assertSee('form')
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('Se connecter');
    }

    public function test_the_home_page_is_available_and_links_to_login(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('PointageApp')
            ->assertSee('Bienvenue')
            ->assertSee('href="'.route('login').'"', false);
    }
}
