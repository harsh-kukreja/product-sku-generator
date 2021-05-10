<?php


namespace App\Queries;


use App\Models\BaseProduct;
use Illuminate\Support\Collection;

class BaseProductQuery {

    /**
     * Gives all the base product values with their stock
     * @return Collection
     */
    public function getAllBaseProductsWithStock(): Collection {
        return BaseProduct::all();
    }
}
