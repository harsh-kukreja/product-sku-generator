<?php


namespace App\Queries;


use App\Models\BaseProduct;
use Illuminate\Support\Collection;

class BaseProductQuery {

    /**
     * Gives all the base product values
     * @return Collection
     */
    public function getAllBaseProducts(): Collection {
        return BaseProduct::all();
    }
}
