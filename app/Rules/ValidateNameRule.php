<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class ValidateNameRule implements ValidationRule
{
    /**
     * @param int $maximumCharacters
     * @param int $minimumCharacters
     * @param string|null $table Table name for uniqueness check
     * @param array|null $extraConditions Extra columns for uniqueness, value can be null to pull from request
     */
    public function __construct(
        protected int $maximumCharacters = 50,
        protected int $minimumCharacters = 3,
        protected ?string $table = null,
        protected ?array $extraConditions = null
    ) { }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = trim($value);

        // Length checks
        if (strlen($value) < $this->minimumCharacters) {
            $fail(":attribute should be at least $this->minimumCharacters characters.");
            return;
        }

        if (strlen($value) > $this->maximumCharacters) {
            $fail(":attribute should not exceed $this->maximumCharacters characters.");
            return;
        }

        // Letters & spaces only
        if (! preg_match('/^[a-zA-Z\s]+$/', $value)) {
            $fail(':attribute can only contain letters and spaces.');
        }

        // Optional uniqueness check
        if ($this->table) {
            // allow for table name case-insensitive
            $query = DB::table($this->table)
                ->whereRaw("LOWER($attribute) = ?", [strtolower($value)]);

            if ($this->extraConditions) {
                foreach ($this->extraConditions as $column => $val) {
                    $conditionValue = $val ?? request()->input($column, null);
                    if (!is_null($conditionValue)) {
                        $query->whereRaw("LOWER($column) = ?", [strtolower($conditionValue)]);
                    }
                }
            }

            if ($query->exists()) {
                $fail("An entry with this $attribute already exists.");
            }
        }
    }
}
