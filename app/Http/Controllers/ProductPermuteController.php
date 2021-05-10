<?php

namespace App\Http\Controllers;

use App\Helpers\ViewHelper;
use App\Http\Controllers\Constants\ProductPermuteControllerConstants;
use App\Http\Requests\ProductPermuteUpdateRequest;
use App\Models\BaseProduct;
use App\Models\ProductPermute;
use App\Models\ProductVariantPermute;
use App\Queries\ProductPermuteQuery;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
     * Shows all the products
     * @param BaseProduct $baseProduct
     * @return View
     */
    public function index(BaseProduct $baseProduct): View {
        //To dynamically populate columns in the datatables according to the variants of the product
        $productPermuteVariants = $this->productPermuteQuery->getAllProductVariants($baseProduct->id)->all();
        return view('product.view', ['productId' => $baseProduct->id, 'product_name' => $baseProduct->name,
            'variants'=> $productPermuteVariants]);
    }


    /**
     * Edit page for Product Permute
     * @param BaseProduct $baseProduct
     * @param ProductPermute $productPermute
     * @return View
     */
    public function edit(BaseProduct $baseProduct, ProductPermute $productPermute): View {
        return view('product.edit', compact(['baseProduct', 'productPermute']));
    }

    /**
     * Updates the value given by product Permute
     * @param BaseProduct $baseProduct
     * @param ProductPermute $productPermute
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(BaseProduct  $baseProduct, ProductPermute $productPermute, ProductPermuteUpdateRequest $request) {
        $validatedData = $request->validated();
        if ($request->has('product_image')) {
            $image = $request->file('product_image');
            $imageName = $productPermute->sku . '-'. time() . '.' .
                $image->getClientOriginalExtension();
            Storage::disk('public')->putFileAs("/images/products/", $image, $imageName, 'public');
            $imageUrl = Storage::url("/images/products/" . $imageName);
            $productPermute->image_url = $imageUrl;
        }
        $productPermute->stock = $validatedData['product_stock'];
        $productPermute->price = ($baseProduct->price + $request['product_price']);
        $productPermute->description = $validatedData['product_description'];
        $productPermute->updated_by = Auth::user()->id;
        $productPermute->update();
        return redirect()->back()->with('message', self::SKU_UPDATE_SUCCESSFULLY);

    }

    /**
     * Remove the specified ProductPermute from storage.
     * @param BaseProduct $baseProduct
     * @param ProductPermute $productPermute
     * @return RedirectResponse
     */
    public function destroy(BaseProduct $baseProduct, ProductPermute $productPermute): RedirectResponse {
        ProductVariantPermute::where('product_id', $productPermute->id)->firstOrFail()->delete();
        $productPermute->delete();
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
                "stock" => $productPermute[$i]->stock,
                "image_url" => $productPermute[$i]->image_url,
                "description" => $productPermute[$i]->description,
                "price" => $productPermute[$i]->price,
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
            ->addColumn("edit", function ($result) use(&$id) {
                return ViewHelper::controlLinkButton("fa fa-pencil-alt", "btn-success text-white", $result['id'], "/product/$id/sku/".$result['id']."/edit", "edit");
            })
            ->editColumn("description", function ($result) {
                if (strlen($result['description']) !== 0) {
                    return $result['description'];
                }
                return self::CLICK_ON_EDIT_FOR_DESCRIPTION;
            })
            ->editColumn("image", function ($result) {
                if ($result["image_url"] !== null && strlen($result["image_url"]) != 0) {
                    return ViewHelper::controlImage($result['image_url']);
                }
                return self::CLICK_ON_EDIT_FOR_ADD_IMAGE;
            })
            ->rawColumns(["edit", "delete", "image"])
            ->make(true);
    }
}
