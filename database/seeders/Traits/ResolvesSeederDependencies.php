<?php

namespace Database\Seeders\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

trait ResolvesSeederDependencies
{
    /**
     * @template T of Model
     *
     * @param class-string<T> $modelClass
     * @param class-string<Seeder> $seederClass
     *
     * @return Collection<int, T>
     */
    protected function resolveDependency(string $modelClass, string $seederClass): Collection
    {
        if ($modelClass::count() === 0) {
            $this->call($seederClass);
        }

        /** @phpstan-ignore-next-line return.type */
        return $modelClass::all();
    }
}
