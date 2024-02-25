<?php

namespace App\Models\QueryBuilder;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Spatie\QueryBuilder\AllowedSort;

class AllowedSorts
{
    /**
     * @var AllowedSort[]
     */
    private array $allowedSorts = [];

    /**
     * @var array<string,string>
     */
    private array $namesToLabels = [];

    private function addLabel(string $name, string $label): self
    {
        $this->namesToLabels[$name] = $label;

        return $this;
    }

    public function addOneDirection(string $label, AllowedSort $allowedSort): self
    {
        $this->allowedSorts[] = $allowedSort;

        return $this->addLabel($allowedSort->getName(), $label);
    }

    public function addBothDirections(string $label, AllowedSort $allowedSort): self
    {
        $this->allowedSorts[] = $allowedSort;

        return $this
            ->addLabel($allowedSort->getName(), $label . ' ' . __('ascending'))
            ->addLabel('-' . $allowedSort->getName(), $label . ' ' . __('descending'));
    }

    /**
     * @return AllowedSort[]
     */
    public function getAllowedSorts(): array
    {
        return $this->allowedSorts;
    }

    /**
     * @return array<string,string>
     */
    public function getNamesWithLabels(): array
    {
        return $this->namesToLabels;
    }

    public function getRule(): In
    {
        return Rule::in(array_keys($this->namesToLabels));
    }

    public function merge(AllowedSorts $allowedSorts): self
    {
        $this->allowedSorts = [
            ...$this->allowedSorts,
            ...$allowedSorts->getAllowedSorts(),
        ];

        $this->namesToLabels = [
            ...$this->namesToLabels,
            ...$allowedSorts->getNamesWithLabels(),
        ];

        return $this;
    }
}
