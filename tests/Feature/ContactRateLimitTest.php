<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ContactRateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('contact:127.0.0.1');
    }

    public function test_contact_form_saves_valid_message(): void
    {
        $response = $this->post('/contact', [
            'name' => 'John Recruiter',
            'email' => 'john@company.com',
            'subject' => 'Interview Inquiry',
            'message' => 'Hello Baruna, we are impressed with your portfolio architecture and would love to chat.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contact_messages', [
            'name' => 'John Recruiter',
            'email' => 'john@company.com',
            'subject' => 'Interview Inquiry',
            'status' => 'unread',
        ]);
    }

    public function test_contact_form_rejects_honeypot_spam(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Spam Bot',
            'email' => 'bot@spammer.com',
            'subject' => 'Buy Crypto',
            'message' => 'Spam message text that should be rejected by honeypot validation.',
            'honeypot' => 'i am a bot',
        ]);

        $response->assertSessionHasErrors('honeypot');
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_form_enforces_rate_limiting_after_three_attempts(): void
    {
        $payload = [
            'name' => 'Valid Sender',
            'email' => 'valid@example.com',
            'subject' => 'Opportunity',
            'message' => 'This is a genuine inquiry message about software development work.',
        ];

        // 1st request
        $this->post('/contact', $payload)->assertSessionHas('success');
        // 2nd request
        $this->post('/contact', $payload)->assertSessionHas('success');
        // 3rd request
        $this->post('/contact', $payload)->assertSessionHas('success');

        // 4th request must be blocked by rate limiter
        $blockedResponse = $this->post('/contact', $payload);

        // Either caught by application RateLimiter (session error) or throttle middleware (429)
        $this->assertTrue(
            $blockedResponse->status() === 429 ||
            $blockedResponse->isRedirection() && session()->has('errors')
        );

        $this->assertDatabaseCount('contact_messages', 3);
    }
}
