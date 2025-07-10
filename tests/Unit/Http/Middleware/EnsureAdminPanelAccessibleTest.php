<?php

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\EnsureAdminPanelAccessible;
use App\Models\Domain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EnsureAdminPanelAccessibleTest extends TestCase
{
    use RefreshDatabase;

    private EnsureAdminPanelAccessible $middleware;

    private Domain $defaultDomain;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new EnsureAdminPanelAccessible;

        // Create a default domain with admin panel access to satisfy DomainObserver validation
        $this->defaultDomain = Domain::factory()->create([
            'host' => 'default-test-domain.com',
            'protocol' => 'https',
            'is_active' => true,
            'is_admin_panel_available' => true,
        ]);
    }

    #[Test]
    public function it_allows_access_when_no_current_domain_and_no_domains_exist(): void
    {
        // Arrange - Remove all domains from database
        Domain::query()->delete();
        $this->assertDatabaseEmpty('domains');

        $request = Request::create('https://unknown-domain.com/admin');
        $next = function ($request) {
            return new Response('Success', 200);
        };

        // Act
        $response = $this->middleware->handle($request, $next);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getContent());
    }

    #[Test]
    public function it_denies_access_when_no_current_domain_but_domains_exist(): void
    {
        // Arrange - Create a domain but request from different domain
        Domain::factory()->create([
            'host' => 'example.com',
            'protocol' => 'https',
            'is_active' => true,
            'is_admin_panel_available' => true,
        ]);

        $request = Request::create('https://unknown-domain.com/admin');
        $next = function ($request) {
            return new Response('Success', 200);
        };

        // Act & Assert
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->middleware->handle($request, $next);
    }

    #[Test]
    public function it_allows_access_when_current_domain_has_admin_panel_available(): void
    {
        // Arrange
        $testDomain = Domain::factory()->create([
            'host' => 'example.com',
            'protocol' => 'https',
            'is_active' => true,
            'is_admin_panel_available' => true,
        ]);

        // Mock the request for current_domain() helper
        $this->app->instance('request', Request::create('https://example.com/admin'));

        $request = Request::create('https://example.com/admin');
        $next = function ($request) {
            return new Response('Success', 200);
        };

        // Act
        $response = $this->middleware->handle($request, $next);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getContent());
    }

    #[Test]
    public function it_denies_access_when_current_domain_has_admin_panel_unavailable(): void
    {
        // Arrange - Create a domain without admin panel access
        // (defaultDomain already exists with admin panel access to satisfy observer)
        $testDomain = Domain::factory()->create([
            'host' => 'example.com',
            'protocol' => 'https',
            'is_active' => true,
            'is_admin_panel_available' => false,
        ]);

        // Mock the request for current_domain() helper
        $this->app->instance('request', Request::create('https://example.com/admin'));

        $request = Request::create('https://example.com/admin');
        $next = function ($request) {
            return new Response('Success', 200);
        };

        // Act & Assert
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->middleware->handle($request, $next);
    }

    #[Test]
    public function it_works_with_http_protocol(): void
    {
        // Arrange
        $testDomain = Domain::factory()->create([
            'host' => 'example.com',
            'protocol' => 'http',
            'is_active' => true,
            'is_admin_panel_available' => true,
        ]);

        // Mock the request for current_domain() helper
        $this->app->instance('request', Request::create('http://example.com/admin'));

        $request = Request::create('http://example.com/admin');
        $next = function ($request) {
            return new Response('Success', 200);
        };

        // Act
        $response = $this->middleware->handle($request, $next);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getContent());
    }

    #[Test]
    public function it_works_with_domain_with_port(): void
    {
        // Arrange
        $testDomain = Domain::factory()->create([
            'host' => 'example.com:8080',
            'protocol' => 'https',
            'is_active' => true,
            'is_admin_panel_available' => true,
        ]);

        // Mock the request for current_domain() helper
        $this->app->instance('request', Request::create('https://example.com:8080/admin'));

        $request = Request::create('https://example.com:8080/admin');
        $next = function ($request) {
            return new Response('Success', 200);
        };

        // Act
        $response = $this->middleware->handle($request, $next);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getContent());
    }

    #[Test]
    public function it_denies_access_for_inactive_domain_with_admin_panel_available(): void
    {
        // Arrange - Create an inactive domain
        // (defaultDomain already exists with admin panel access to satisfy observer)
        $testDomain = Domain::factory()->create([
            'host' => 'example.com',
            'protocol' => 'https',
            'is_active' => false,
            'is_admin_panel_available' => true,
        ]);

        $request = Request::create('https://example.com/admin');
        $next = function ($request) {
            return new Response('Success', 200);
        };

        // Act & Assert
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->middleware->handle($request, $next);
    }

    #[Test]
    public function it_handles_multiple_domains_correctly(): void
    {
        // Arrange - Create multiple domains
        // (defaultDomain already exists with admin panel access to satisfy observer)
        $domain1 = Domain::factory()->create([
            'host' => 'domain1.com',
            'protocol' => 'https',
            'is_active' => true,
            'is_admin_panel_available' => false,
        ]);

        $domain2 = Domain::factory()->create([
            'host' => 'domain2.com',
            'protocol' => 'https',
            'is_active' => true,
            'is_admin_panel_available' => true,
        ]);

        $domain3 = Domain::factory()->create([
            'host' => 'domain3.com',
            'protocol' => 'https',
            'is_active' => true,
            'is_admin_panel_available' => false,
        ]);

        $next = function ($request) {
            return new Response('Success', 200);
        };

        // Test domain2.com (should allow)
        $this->app->instance('request', Request::create('https://domain2.com/admin'));
        $request2 = Request::create('https://domain2.com/admin');
        $response2 = $this->middleware->handle($request2, $next);
        $this->assertEquals(200, $response2->getStatusCode());

        // Test domain1.com (should deny)
        $this->app->instance('request', Request::create('https://domain1.com/admin'));
        $request1 = Request::create('https://domain1.com/admin');
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->middleware->handle($request1, $next);
    }

    #[Test]
    public function it_passes_request_to_next_middleware_when_allowed(): void
    {
        // Arrange
        $testDomain = Domain::factory()->create([
            'host' => 'example.com',
            'protocol' => 'https',
            'is_active' => true,
            'is_admin_panel_available' => true,
        ]);

        // Mock the request for current_domain() helper
        $this->app->instance('request', Request::create('https://example.com/admin'));

        $request = Request::create('https://example.com/admin');
        $nextCalled = false;
        $passedRequest = null;

        $next = function ($req) use (&$nextCalled, &$passedRequest) {
            $nextCalled = true;
            $passedRequest = $req;

            return new Response('Next middleware called', 200);
        };

        // Act
        $response = $this->middleware->handle($request, $next);

        // Assert
        $this->assertTrue($nextCalled);
        $this->assertSame($request, $passedRequest);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Next middleware called', $response->getContent());
    }

    #[Test]
    public function it_returns_404_status_code_when_access_denied(): void
    {
        // Arrange - Create a domain without admin panel access
        // (defaultDomain already exists with admin panel access to satisfy observer)
        $testDomain = Domain::factory()->create([
            'host' => 'example.com',
            'protocol' => 'https',
            'is_active' => true,
            'is_admin_panel_available' => false,
        ]);

        $request = Request::create('https://example.com/admin');
        $next = function ($request) {
            return new Response('Success', 200);
        };

        // Act & Assert
        try {
            $this->middleware->handle($request, $next);
            $this->fail('Expected NotFoundHttpException was not thrown');
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            $this->assertEquals(404, $e->getStatusCode());
        }
    }
}
