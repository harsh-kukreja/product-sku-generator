<?php


namespace App\Queries;


use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class ProductVariantQuery {

    /**
     * Gets all the product variants with its values given the base product id
     * @param int $baseProductId
     * @return Collection
     */
    public function getAllProductVariantWithValues(int $baseProductId): Collection {
        return  ProductVariant::query()
            ->join('variant_values', 'product_variants.variant_value_id', '=', 'variant_values.id')
            ->join('variants', 'variant_values.variant_id', '=', 'variants.id')
            ->where('base_product_id', $baseProductId)
            ->select(['variant_values.value', 'variants.name', 'product_variants.variant_value_id'])
            ->get();
    }
}
