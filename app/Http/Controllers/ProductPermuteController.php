<?php

namespace App\Http\Controllers;

use App\Helpers\ViewHelper;
use App\Http\Controllers\Constants\ProductPermuteControllerConstants;
use App\Models\ProductPermute;
use App\Models\ProductVariantPermute;
use App\Queries\ProductPermuteQuery;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\DataTables;

class ProductPermuteController extends Controller  implements ProductPermuteControllerConstants {

    protected ProductPermuteQuery $productPermuteQuery;

    /**
     * ProductPermuteController constructor.
     * @param ProductPermuteQuery $productPermuteQuery
     */
    public function __construct(ProductPermuteQuery $productPermuteQuery) {
        $this->productPermuteQuery = $productPermuteQuery;
    }

    /**
     * Remove the specified ProductPermute from storage.
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse {
        ProductPermute::findOrFail($id)->delete();
        ProductVariantPermute::where('product_id', $id)->firstOrFail()->delete();
        return redirect()->back()->with('message', self::SKU_DELETED_SUCCESSFULLY);
    }

    /**
     * @param int $id
     * @return DataTables
     * @throws \Exception
     */
    public function datatables(int $id) {
        $productPermute = $this->productPermuteQuery->getAllProductVariantDetails($id);
        $variantCount =  $productPermute->unique('name')->count();

        $result = array();
        //Iterating to create sku with its unique product variant
        for ($i = 0, $counter = 0; $i < $productPermute->count(); $i += $variantCount, $counter++) {
            $product = [
                "sku" => $productPermute[$i]->sku,
                "id" => $productPermute[$i]->id,
            ];

            $productVariants = array();

            //Iterating to get the product variant under the same key
            for ($j = 0; $j < $variantCount; $j++) {
                $productVariants[$productPermute[$i+$j]->name] = $productPermute[$i+$j]->value;
            }
            $product = array_merge($product, $productVariants);
            $result[$counter] =  $product;
        }
        return DataTables::of($result)
            ->addColumn("delete", function ($result) {
                return ViewHelper::controlModalButton("fa fa-trash-alt", "btn-danger", $result['id'], "delete", "deleteModal");
            })
            ->rawColumns(["delete"])
            ->make(true);
    }
}
