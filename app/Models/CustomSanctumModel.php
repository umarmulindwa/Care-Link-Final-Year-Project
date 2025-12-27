<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

use Laravel\Sanctum\PersonalAccessToken;

class CustomSanctumModel extends PersonalAccessToken
{
    use HasApiTokens;
    protected $connection = 'mysql';
    protected $hidden = [];

    /**
     * Assign an ability to the token.
     *
     * @param string $ability
     * @return void
     */
    public function assignAbility($ability)
    {
        // Check if the token already has this ability to avoid duplicates
        if (!$this->hasAbility($ability)) {
            // Get the existing abilities associated with the token
            $existingAbilities = $this->abilities ?? [];

            // Add the new ability to the existing abilities array
            $existingAbilities[] = $ability;

            // Update the token's abilities property with the modified array
            $this->update(['abilities' => $existingAbilities]);
        }
    }

    /**
     * Check if the token has a specific ability.
     *
     * @param string $ability
     * @return bool
     */
    public function hasAbility($ability)
    {
        return in_array($ability, $this->abilities ?? []);
    }
    /**
     * Revoke a specific ability from the token.
     *
     * @param string $ability
     * @return void
     */
    public function revokeAbility($ability)
    {
        // Get the existing abilities associated with the token
        $existingAbilities = $this->abilities ?? [];

        // Find the index of the ability to be revoked in the array
        $index = array_search($ability, $existingAbilities);

        if ($index !== false) {
            // Remove the ability from the array
            array_splice($existingAbilities, $index, 1);

            // Update the token's abilities property with the modified array
            $this->update(['abilities' => $existingAbilities]);
        }
    }


}
