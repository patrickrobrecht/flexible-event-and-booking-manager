<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Http\Controllers\MaterialController;
use App\Http\Requests\Filters\MaterialFilterRequest;
use App\Http\Requests\MaterialRequest;
use App\Models\Material;
use App\Policies\MaterialPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(Material::class)]
#[CoversClass(MaterialController::class)]
#[CoversClass(MaterialFilterRequest::class)]
#[CoversClass(MaterialPolicy::class)]
#[CoversClass(MaterialRequest::class)]
class MaterialControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanViewMaterialsOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/materials', Ability::ViewMaterials);
    }

    public function testUserCanViewSingleMaterialOnlyWithCorrectAbility(): void
    {
        $material = self::createMaterial();
        $this->assertUserCanGetOnlyWithAbility("/materials/{$material->id}", Ability::ViewMaterials);
    }

    public function testUserCanOpenCreateMaterialFormOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/materials/create', Ability::CreateMaterials);
    }

    public function testUserCanStoreMaterialOnlyWithCorrectAbility(): void
    {
        $data = self::makeMaterialData();

        $this->assertUserCanPostOnlyWithAbility('/materials', $data, Ability::CreateMaterials, null);
    }

    public function testUserCanOpenEditMaterialFormOnlyWithCorrectAbility(): void
    {
        $material = self::createMaterial();
        $this->assertUserCanGetOnlyWithAbility("/materials/{$material->id}/edit", Ability::EditMaterials);
    }

    public function testUserCanUpdateMaterialOnlyWithCorrectAbility(): void
    {
        $material = self::createMaterial();
        $data = self::makeMaterialData();

        $editRoute = "/materials/{$material->id}/edit";
        $this->assertUserCanPutOnlyWithAbility("/materials/{$material->id}", $data, Ability::EditMaterials, $editRoute, $editRoute);
    }

    public function testUserCanDeleteMaterialOnlyWithCorrectAbility(): void
    {
        $material = self::createMaterial();

        $this->assertUserCanDeleteOnlyWithAbility("/materials/{$material->id}", Ability::DestroyMaterials, '/materials');
        $this->assertDatabaseMissing('materials', ['id' => $material->id]);
    }

    /**
     * @return array<string, mixed>
     */
    private static function makeMaterialData(): array
    {
        return [
            ...self::makeData(Material::factory()),
            'organization_id' => self::createOrganization()->id,
        ];
    }
}
