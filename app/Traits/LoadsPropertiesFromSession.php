<?php

namespace App\Traits;

use Illuminate\Support\Facades\Session;

/**
 * @property-read array<string,string> $propertiesSavedInSession
 */
trait LoadsPropertiesFromSession
{
    abstract public function getSessionKey(string $propertyName): string;

    /**
     * @return int[]|string[]|string|null
     */
    private function getValidatedValue(string $propertyName, string $expectedType): array|string|null
    {
        $value = Session::get($this->getSessionKey($propertyName));

        return match ($expectedType) {
            'int[]', => is_array($value) ? array_map('intval', $value) : null,
            'string' => is_string($value) ? $value : null,
            'string[]' => is_array($value) ? $value : null,
            default => null,
        };
    }

    public function loadSettingsFromSession(): void
    {
        foreach ($this->propertiesSavedInSession ?? [] as $propertyName => $expectedType) {
            $value = $this->getValidatedValue($propertyName, $expectedType);
            if (isset($value)) {
                $this->$propertyName = $value;
            }
        }
    }

    public function storeSettingsInSession(): void
    {
        foreach (array_keys($this->propertiesSavedInSession ?? []) as $propertyName) {
            Session::put($this->getSessionKey($propertyName), $this->{$propertyName});
        }
    }
}
