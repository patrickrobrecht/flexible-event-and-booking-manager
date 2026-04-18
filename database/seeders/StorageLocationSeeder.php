<?php

namespace Database\Seeders;

use App\Models\StorageLocation;
use Illuminate\Database\Seeder;
use Random\RandomException;

class StorageLocationSeeder extends Seeder
{
    /**
     * @throws RandomException
     */
    public function run(): void
    {
        foreach (range(1, 5) as $index) {
            $parent = StorageLocation::factory()->create([
                'name' => 'Warehouse ' . $index,
            ]);

            $this->createChildren($parent, 1);
        }
    }

    /**
     * @throws RandomException
     */
    private function createChildren(StorageLocation $parent, int $currentLevel): void
    {
        if ($currentLevel >= 3) {
            return;
        }

        $childCount = random_int(0, 4);
        $prefix = match ($currentLevel) {
            1 => 'Shelf ',
            2 => 'Bin ',
            default => 'Level ',
        };

        for ($i = 1; $i <= $childCount; $i++) {
            $child = StorageLocation::factory()->create([
                'name' => $parent->name . ' - ' . $prefix . $i,
                'parent_storage_location_id' => $parent->id,
            ]);

            $this->createChildren($child, $currentLevel + 1);
        }
    }
}
