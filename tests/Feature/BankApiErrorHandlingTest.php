<?php

namespace Tests\Feature;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BankApiErrorHandlingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_login_shows_error_when_bank_is_unreachable(): void
    {
        Http::fake(fn () => throw new ConnectionException('timed out'));

        $this->from('/login')
            ->post('/login', ['username' => 'u', 'pin' => '1234', 'telegram_init_data' => 'x'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('login');
    }

    public function test_internal_transfer_shows_error_when_bank_is_unreachable(): void
    {
        Http::fake(fn () => throw new ConnectionException('timed out'));

        $this->withSession(['token' => 't'])
            ->from('/internal-fund')
            ->post('/internal-fund', ['accountFrom' => 'A', 'accountTo' => 'B', 'amount' => 10])
            ->assertRedirect('/internal-fund')
            ->assertSessionHas('error');
    }

    public function test_internal_transfer_sends_user_note_as_remark(): void
    {
        Http::fake(['*/transfer' => Http::response(['traceId' => '1'], 200)]);

        $this->withSession(['token' => 't', 'accounts' => [['cusShoName' => 'Test']]])
            ->post('/internal-fund', ['accountFrom' => 'A', 'accountTo' => 'B', 'amount' => 10, 'note' => 'إيجار'])
            ->assertOk()
            ->assertSee('إيجار');

        Http::assertSent(fn ($r) => $r['remarkDebit'] === 'إيجار' && $r['remarkCredit'] === 'إيجار');
    }

    public function test_customer_transfer_shows_error_when_bank_is_unreachable(): void
    {
        Http::fake(fn () => throw new ConnectionException('timed out'));

        $this->withSession(['token' => 't'])
            ->from('/transfer-internal')
            ->post('/transfer-internal', ['from_account' => 'A', 'bban' => 'B', 'amount' => 10])
            ->assertRedirect('/transfer-internal')
            ->assertSessionHas('error');
    }

    public function test_logout_requires_post_and_clears_session(): void
    {
        $this->withSession(['token' => 't'])->get('/logout')->assertStatus(405);

        $this->withSession(['token' => 't'])
            ->post('/logout')
            ->assertRedirect('/login')
            ->assertSessionMissing('token');
    }
}
