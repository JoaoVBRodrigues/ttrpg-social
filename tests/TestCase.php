<?php

namespace Tests;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use Livewire\Component;
use Livewire\Mechanisms\ComponentRegistry;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        $appKey = 'base64:ZKVGQfpMvyd5/hFLM83Arxw/fuSHfnEwAkpIBMD8H6E=';

        putenv('APP_ENV=testing');
        putenv('APP_KEY='.$appKey);
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');
        putenv('CACHE_STORE=array');
        putenv('SESSION_DRIVER=array');
        putenv('QUEUE_CONNECTION=sync');
        putenv('MAIL_MAILER=array');
        putenv('BROADCAST_CONNECTION=null');

        $_ENV['APP_ENV'] = 'testing';
        $_ENV['APP_KEY'] = $appKey;
        $_ENV['DB_CONNECTION'] = 'sqlite';
        $_ENV['DB_DATABASE'] = ':memory:';
        $_ENV['CACHE_STORE'] = 'array';
        $_ENV['SESSION_DRIVER'] = 'array';
        $_ENV['QUEUE_CONNECTION'] = 'sync';
        $_ENV['MAIL_MAILER'] = 'array';
        $_ENV['BROADCAST_CONNECTION'] = 'null';

        $_SERVER['APP_ENV'] = 'testing';
        $_SERVER['APP_KEY'] = $appKey;
        $_SERVER['DB_CONNECTION'] = 'sqlite';
        $_SERVER['DB_DATABASE'] = ':memory:';
        $_SERVER['CACHE_STORE'] = 'array';
        $_SERVER['SESSION_DRIVER'] = 'array';
        $_SERVER['QUEUE_CONNECTION'] = 'sync';
        $_SERVER['MAIL_MAILER'] = 'array';
        $_SERVER['BROADCAST_CONNECTION'] = 'null';

        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
        $this->registerLivewireTestingMacros();
    }

    protected function registerLivewireTestingMacros(): void
    {
        if (! TestResponse::hasMacro('assertSeeLivewire')) {
            TestResponse::macro('assertSeeLivewire', function (string $component) {
                if (is_subclass_of($component, Component::class)) {
                    $component = app(ComponentRegistry::class)->getName($component);
                }

                $escapedComponentName = trim(htmlspecialchars(json_encode(['name' => $component])), '{}');

                \PHPUnit\Framework\Assert::assertStringContainsString(
                    $escapedComponentName,
                    $this->getContent(),
                    'Cannot find Livewire component ['.$component.'] rendered on page.'
                );

                return $this;
            });
        }

        if (! TestResponse::hasMacro('assertDontSeeLivewire')) {
            TestResponse::macro('assertDontSeeLivewire', function (string $component) {
                if (is_subclass_of($component, Component::class)) {
                    $component = app(ComponentRegistry::class)->getName($component);
                }

                $escapedComponentName = trim(htmlspecialchars(json_encode(['name' => $component])), '{}');

                \PHPUnit\Framework\Assert::assertStringNotContainsString(
                    $escapedComponentName,
                    $this->getContent(),
                    'Found Livewire component ['.$component.'] rendered on page.'
                );

                return $this;
            });
        }
    }
}
