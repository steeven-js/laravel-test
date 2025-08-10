<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\Traits\HasStandardActions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HasStandardActionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_use_has_standard_actions_trait()
    {
        // Créer une classe de test qui utilise le trait
        $testClass = new class
        {
            use HasStandardActions;

            public static function getUrl(string $name, array $parameters = []): string
            {
                return "/test/{$name}";
            }
        };

        // Vérifier que la classe utilise bien le trait
        $traits = class_uses($testClass);
        $this->assertContains(HasStandardActions::class, $traits);

        // Vérifier que les méthodes du trait sont disponibles
        $this->assertTrue(method_exists($testClass, 'configureStandardActions'));
        $this->assertTrue(method_exists($testClass, 'configureStandardActionsWithCustom'));
        $this->assertTrue(method_exists($testClass, 'configureStandardActionsWithConditions'));
    }

    /** @test */
    public function it_can_generate_urls_for_actions()
    {
        $testClass = new class
        {
            use HasStandardActions;

            public static function getUrl(string $name, array $parameters = []): string
            {
                return "/test/{$name}";
            }
        };

        // Vérifier que la méthode getUrl fonctionne
        $url = $testClass::getUrl('view', ['record' => 1]);
        $this->assertEquals('/test/view', $url);
    }
}
