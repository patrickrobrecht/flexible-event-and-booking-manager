<?php

namespace App\Models\QueryBuilder;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Spatie\QueryBuilder\AllowedSort;

class SortOptions
{
    /**
     * @var AllowedSort[]
     */
    private array $allowedSorts = [];

    /**
     * @var AllowedSort[]
     */
    private array $defaultSorts = [];

    /**
     * @var array<string,string>
     */
    private array $namesToLabels = [];

    private function addLabel(string $name, string $label): self
    {
        $this->namesToLabels[$name] = $label;

        return $this;
    }

    private function addSort(AllowedSort $allowedSort, bool $asDefault): void
    {
        $this->allowedSorts[] = $allowedSort;

        if ($asDefault) {
            $this->defaultSorts[] = $allowedSort;
        }
    }

    public function addAscending(string $label, AllowedSort $allowedSort, bool $asDefault = false): self
    {
        $this->addSort($allowedSort, $asDefault);

        return $this->addLabel($allowedSort->getName(), $label);
    }

    public function addBothDirections(string $label, AllowedSort $allowedSort, bool $asDefault = false): self
    {
        $this->addSort($allowedSort, $asDefault);

        return $this
            ->addLabel($allowedSort->getName(), $label . ' ' . __('ascending'))
            ->addLabel('-' . $allowedSort->getName(), $label . ' ' . __('descending'));
    }

    public function addDescending(string $label, AllowedSort $allowedSort, bool $asDefault = false): self
    {
        $this->addSort($allowedSort, $asDefault);

        return $this->addLabel('-' . $allowedSort->getName(), $label);
    }

    /**
     * @return AllowedSort[]
     */
    public function getAllowedSorts(): array
    {
        return $this->allowedSorts;
    }

    /**
     * @return AllowedSort[]
     */
    public function getDefaultSorts(): array
    {
        return $this->defaultSorts;
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

    public function merge(self $allowedSorts): self
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
