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
