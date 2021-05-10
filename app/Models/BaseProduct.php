<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class BaseProduct extends BaseModel {
    public const VARIANT = 1;
    public const NO_VARIANT  = 0;

    /**
     * Adding Relation of product permute
     * @return HasMany
     */
    public function productPermutes(): HasMany {
        return $this->hasMany(ProductPermute::class);
    }
}
