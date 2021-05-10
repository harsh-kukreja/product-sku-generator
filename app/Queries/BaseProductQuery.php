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
        return BaseProduct::query()
            ->join('product_permutes', 'base_products.id', '=','product_permutes.base_product_id')
            ->selectRaw('sum(product_permutes.stock) as stock,
                base_products.name,
                base_products.id,
                base_products.price,
                base_products.description,
                base_products.image_url,
                base_products.has_variant')
            ->groupBy([ 'base_products.name' ,
                'product_permutes.base_product_id',
                'base_products.id',
                'base_products.price',
                'base_products.description',
                'base_products.image_url',
                'base_products.has_variant'])
            ->get();
    }
}
