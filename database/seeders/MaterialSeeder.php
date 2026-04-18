<?php

namespace Database\Seeders;

use App\Enums\MaterialStatus;
use App\Models\Material;
use App\Models\Organization;
use App\Models\StorageLocation;
use Database\Seeders\Traits\ResolvesSeederDependencies;
use Illuminate\Database\Seeder;
use Random\RandomException;

class MaterialSeeder extends Seeder
{
    use ResolvesSeederDependencies;

    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $organizations = $this->resolveDependency(Organization::class, OrganizationSeeder::class);
        $storageLocations = $this->resolveDependency(StorageLocation::class, StorageLocationSeeder::class);

        foreach ($organizations as $organization) {
            $materials = Material::factory(random_int(10, 20))
                ->state(function () {
                    return ['name' => fake()->unique()->words(3, true)];
                })
                ->for($organization)
                ->create();

            foreach ($materials as $material) {
                $chance = fake()->numberBetween(1, 100);

                // 90% get at least one storage location
                if ($chance <= 90) {
                    $locationsToAttach = $storageLocations->random(1);

                    // For 20% of all materials, 2-4 storage locations more are added.
                    if (fake()->numberBetween(1, 100) <= 20) {
                        $additionalCount = random_int(2, 4);
                        $additionalLocations = $storageLocations
                            ->whereNotIn('id', $locationsToAttach->pluck('id'))
                            ->random(min($storageLocations->count() - 1, $additionalCount));
                        $locationsToAttach = $locationsToAttach->concat($additionalLocations);
                    }

                    foreach ($locationsToAttach as $location) {
                        $material->storageLocations()->attach($location->id, [
                            'material_status' => fake()->randomElement(MaterialStatus::cases()),
                            'stock' => fake()->optional()->numberBetween(1, 100),
                            'remarks' => fake()->optional()->text(),
                        ]);
                    }
                }
            }
        }
    }
}
