<?php

namespace Tests\Feature\Livewire\Materials;

use App\Livewire\Materials\MaterialSearch;
use App\Models\Material;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(Material::class)]
#[CoversClass(MaterialSearch::class)]
class MaterialSearchTest extends TestCase
{
    public function testFilteredMaterialSearch(): void
    {
        // Create materials with different names and descriptions.
        $matchingMaterial1 = Material::factory()->forOrganization()->create([
            'name' => 'Test Material',
            'description' => 'This is a test material',
        ]);
        $matchingMaterial2 = Material::factory()->forOrganization()->create([
            'name' => 'Another Material',
            'description' => 'This contains test keyword',
        ]);
        $nonMatchingMaterial = Material::factory()->forOrganization()->create([
            'name' => 'Different Item',
            'description' => 'This does not match',
        ]);

        // Test search with a term that should match two materials.
        Livewire::test(MaterialSearch::class)
            ->set('search', 'test')
            ->assertOk()
            ->assertSee([$matchingMaterial1->name, $matchingMaterial2->name])
            ->assertDontSee($nonMatchingMaterial->name);

        // Test search term matching two materials and additional filter for organization.
        Livewire::test(MaterialSearch::class)
            ->set('search', 'test')
            ->set('organization_id', $matchingMaterial1->organization_id)
            ->assertOk()
            ->assertSee($matchingMaterial1->name)
            ->assertDontSee([$matchingMaterial2->name, $nonMatchingMaterial->name]);

        // Test search with a term that should match only one material.
        Livewire::test(MaterialSearch::class)
            ->set('search', 'Another')
            ->assertOk()
            ->assertSee($matchingMaterial2->name)
            ->assertDontSee([$matchingMaterial1->name, $nonMatchingMaterial->name]);

        // Test search with a term that should not match any materials.
        Livewire::test(MaterialSearch::class)
            ->set('search', 'nonexistent')
            ->assertOk()
            ->assertDontSee([$matchingMaterial1->name, $matchingMaterial2->name, $nonMatchingMaterial->name]);
    }
}
