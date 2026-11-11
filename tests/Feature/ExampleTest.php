<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    // The public search page must open without logging in.
    public function test_the_public_search_page_loads(): void
    {
        $this->get('/')->assertStatus(200);
    }
}
