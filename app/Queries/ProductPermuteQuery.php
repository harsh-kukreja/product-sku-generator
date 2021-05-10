<?php


namespace App\Queries;


use App\Models\ProductPermute;
use Illuminate\Support\Collection;

class ProductPermuteQuery {

    /**
     * Returns all the distinct product variants given the base product id
     * @param int $baseProductId
     * @return Collection
     */
    public function getAllProductVariants(int $baseProductId): Collection {
        return ProductPermute::query()
            ->where('base_product_id', $baseProductId)
            ->join('product_variant_permutes', 'product_permutes.id', '=', 'product_variant_permutes.product_id')
            ->join('variant_values', 'product_variant_permutes.variant_value_id', '=', 'variant_values.id')
            ->join('variants', 'variant_values.variant_id', '=', 'variants.id')
            ->selectRaw('distinct(variants.name)')
            ->get();
    }

    /**
     * Gets all product variant Details
     * @param int $baseProductId
     * @return mixed
     */
    public function getAllProductVariantDetails(int $baseProductId): Collection {
        return ProductPermute::where('base_product_id', $baseProductId)
            ->join('product_variant_permutes', 'product_permutes.id', '=', 'product_variant_permutes.product_id')
            ->join('variant_values', 'product_variant_permutes.variant_value_id', '=', 'variant_values.id')
            ->join('variants', 'variant_values.variant_id', '=', 'variants.id')
            ->select(['product_permutes.sku',
                'product_permutes.id',
                'variant_values.value',
                'variants.name',
                'product_permutes.stock',
                'product_permutes.price',
                'product_permutes.image_url',
                'product_permutes.description'])
            ->get();
    }
}
