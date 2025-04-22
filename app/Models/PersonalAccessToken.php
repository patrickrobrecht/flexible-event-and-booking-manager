<?php

namespace App\Models;

use App\Enums\Ability;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

/**
 * @property-read int $id
 * @property string $name
 * @property string[] $abilities
 * @property-read ?Carbon $last_used_at
 * @property-read ?Carbon $expires_at
 *
 * @property-read User $tokenable {@see SanctumPersonalAccessToken::tokenable()}
 */
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasFactory;

    /**
     * @param array<string, mixed> $validatedData
     */
    public function fillAndSave(array $validatedData): bool
    {
        $this->fill($validatedData);

        return $this->save();
    }

    /**
     * @param array{name: string, abilities: Ability[]|string[]|null, expires_at?: ?string} $validatedData
     */
    public static function createTokenFromValidated(User $tokenable, array $validatedData): NewAccessToken
    {
        return $tokenable->createToken(
            $validatedData['name'],
            $validatedData['abilities'] ?? [],
            isset($validatedData['expires_at'])
                ? Carbon::createFromFormat('Y-m-d\TH:i', $validatedData['expires_at'])
                : null
        );
    }
}
