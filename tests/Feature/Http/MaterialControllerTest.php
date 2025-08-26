<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\MaterialStatus;
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
#[CoversClass(MaterialStatus::class)]
class MaterialControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanViewMaterialsOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/materials', Ability::ViewMaterials);
    }

    public function testUserCanExportMaterialsOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/materials?output=export', Ability::ViewMaterials)
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->assertHeader('content-disposition', 'attachment; filename=Materialien.xlsx');
    }

    public function testUserCanViewMaterialSearchOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/materials/search', Ability::ViewMaterials);
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

    public function testUserCanOpenCreateMaterialWithPreselectedStorageLocationFormOnlyWithCorrectAbility(): void
    {
        $storageLocation = self::createStorageLocation();
        $this->assertUserCanGetOnlyWithAbility("/materials/create?storage_location_id={$storageLocation->id}", Ability::CreateMaterials)
            ->assertSeeHtml("<strong>{$storageLocation->name}</strong>");
    }

    public function testUserCanStoreMaterialOnlyWithCorrectAbility(): void
    {
        $data = self::makeMaterialData();
        $data['storage_locations'] = [
            'new' => $this->makeStorageLocationPivotData(),
        ];
        $this->assertUserCanPostOnlyWithAbility('/materials', $data, Ability::CreateMaterials, null);
        $this->assertDatabaseCount('materials', 1);
        $this->assertDatabaseCount('material_storage_location', 1);
    }

    public function testUserCanOpenEditMaterialFormOnlyWithCorrectAbility(): void
    {
        $material = self::createMaterial();
        $this->assertUserCanGetOnlyWithAbility("/materials/{$material->id}/edit", Ability::EditMaterials);
    }

    public function testUserCanUpdateMaterialOnlyWithCorrectAbility(): void
    {
        $material = self::createMaterial(3);
        [$storageLocationToUpdate, $storageLocationToExchange, $storageLocationToDelete] = $material->storageLocations->all();
        $data = self::makeMaterialData();
        $data['storage_locations'] = [
            'new' => $this->makeStorageLocationPivotData(),
            $storageLocationToUpdate->pivot->id => [
                'storage_location_id' => $storageLocationToUpdate->id,
                ...$storageLocationToUpdate->pivot->toArray(),
            ],
            $storageLocationToExchange->pivot->id => $this->makeStorageLocationPivotData(),
            $storageLocationToDelete->pivot->id => [
                'storage_location_id' => $storageLocationToDelete->id,
                ...$storageLocationToDelete->pivot->toArray(),
                'remove' => 1,
            ],
        ];

        $this->assertUserCanPutOnlyWithAbility(
            "/materials/{$material->id}",
            $data,
            Ability::EditMaterials,
            "/materials/{$material->id}/edit",
            "/materials/{$material->id}"
        );
        $this->assertDatabaseCount('materials', 1);
        $this->assertDatabaseCount('material_storage_location', 3);
        $this->assertDatabaseHas('material_storage_location', [
            'id' => $storageLocationToUpdate->pivot->id, // Entry updated.
            'material_id' => $material->id,
            'storage_location_id' => $data['storage_locations'][$storageLocationToUpdate->pivot->id]['storage_location_id'],
        ]);
        $this->assertDatabaseHas('material_storage_location', [
            'material_id' => $material->id,
            'storage_location_id' => $data['storage_locations'][$storageLocationToExchange->pivot->id]['storage_location_id'],
        ]);
        $this->assertDatabaseMissing('material_storage_location', ['id' => $storageLocationToDelete]);
    }

    public function testUserCanDeleteMaterialOnlyWithCorrectAbility(): void
    {
        $material = self::createMaterial(2);

        $this->assertUserCanDeleteOnlyWithAbility("/materials/{$material->id}", Ability::DestroyMaterials, '/materials');
        $this->assertDatabaseMissing('materials', ['id' => $material->id]);
        $this->assertDatabaseMissing('material_storage_location', ['material_id' => $material->id]);
    }

    /**
     * @return array<string, mixed>
     */
    private function makeStorageLocationPivotData(): array
    {
        return [
            'storage_location_id' => self::createStorageLocation()->id,
            /** @phpstan-ignore-next-line property.nonObject */
            'material_status' => $this->faker->randomElement(MaterialStatus::cases())->value,
            'stock' => $this->faker->optional()->numberBetween(1, 100),
            'remarks' => $this->faker->optional()->text(),
        ];
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
