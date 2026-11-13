<?php

namespace Tests\Feature;

use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_visitor_can_subscribe(): void
    {
        $this->postJson('/api/v1/newsletter', ['email' => 'Farmer@Example.com', 'locale' => 'en'])->assertCreated();

        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'farmer@example.com', 'locale' => 'en']);
    }

    public function test_subscribing_twice_gives_the_same_answer_and_one_row(): void
    {
        $this->postJson('/api/v1/newsletter', ['email' => 'a@example.com'])->assertCreated();
        $this->postJson('/api/v1/newsletter', ['email' => 'a@example.com'])->assertCreated();

        $this->assertSame(1, NewsletterSubscriber::query()->count());
    }

    public function test_invalid_email_is_rejected(): void
    {
        $this->postJson('/api/v1/newsletter', ['email' => 'not-an-email'])->assertUnprocessable();
    }

    public function test_the_form_is_rate_limited(): void
    {
        foreach (range(1, 5) as $i) {
            $this->postJson('/api/v1/newsletter', ['email' => "u{$i}@example.com"])->assertCreated();
        }

        $this->postJson('/api/v1/newsletter', ['email' => 'u6@example.com'])->assertTooManyRequests();
    }
}
