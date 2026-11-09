<?php

namespace Tests\Feature;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProtectedRoutesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
        Http::fake(); // لا نرسل أي طلب حقيقي للبنك
    }

    public static function protectedRoutes(): array
    {
        return [
            'dashboard' => ['GET', '/dashboard'],
            'accounts' => ['GET', '/accounts'],
            'internal fund page' => ['GET', '/internal-fund'],
            'internal fund submit' => ['POST', '/internal-fund'],
            'bban page' => ['GET', '/transfer-bban'],
            'bban verify' => ['POST', '/transfer-bban/verify'],
            'customer transfer page' => ['GET', '/transfer-internal'],
            'customer transfer submit' => ['POST', '/transfer-internal'],
            'customer lookup' => ['GET', '/bank-api/customer/name/123'],
        ];
    }

    #[DataProvider('protectedRoutes')]
    public function test_guest_is_redirected_to_login(string $method, string $uri): void
    {
        $this->call($method, $uri)->assertRedirect('/login');

        Http::assertNothingSent();
    }

    public function test_guest_ajax_lookup_gets_401_json(): void
    {
        $this->getJson('/bank-api/customer/name/123')
            ->assertStatus(401)
            ->assertJson(['success' => false]);

        Http::assertNothingSent();
    }

    public function test_logged_in_customer_can_open_protected_pages(): void
    {
        $session = ['token' => 't', 'accounts' => [[
            'accountNo' => '02100278242001310100', 'ledModelDesc' => 'ادخار',
            'availBal' => '100.00', 'accountStatus' => 'نشط', 'cusShoName' => 'عميل',
        ]]];

        foreach (['/dashboard', '/accounts', '/internal-fund', '/transfer-internal', '/transfer-bban'] as $uri) {
            $this->withSession($session)->get($uri)->assertOk();
        }
    }

    public function test_logout_blocks_protected_pages_again(): void
    {
        $this->withSession(['token' => 't'])->post('/logout');

        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_login_page_stays_public(): void
    {
        $this->get('/login')->assertOk();
    }
}
