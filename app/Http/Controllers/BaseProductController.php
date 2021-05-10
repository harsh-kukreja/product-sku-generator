<?php

namespace App\Http\Controllers;

use App\Helpers\SKUGenerator;
use App\Helpers\ViewHelper;
use App\Http\Controllers\Constants\BaseProductControllerConstants;
use App\Http\Requests\BaseProductRequest;
use App\Models\BaseProduct;
use App\Models\ProductPermute;
use App\Models\ProductVariant;
use App\Models\ProductVariantPermute;
use App\Models\Variant;
use App\Models\VariantValue;
use App\Queries\ProductPermuteQuery;
use App\Queries\ProductVariantQuery;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class BaseProductController extends Controller implements BaseProductControllerConstants {

    protected SKUGenerator $skuGenerator;
    protected ProductVariantQuery $productVariantQuery;
    protected ProductPermuteQuery $productPermuteQuery;

    /**
     * BaseProductController constructor.
     * @param SKUGenerator $skuGenerator
     * @param ProductVariantQuery $productVariantQuery
     * @param ProductPermuteQuery $productPermuteQuery
     */
    public function __construct(SKUGenerator $skuGenerator, ProductVariantQuery $productVariantQuery, ProductPermuteQuery $productPermuteQuery) {
        $this->skuGenerator = $skuGenerator;
        $this->productVariantQuery = $productVariantQuery;
        $this->productPermuteQuery = $productPermuteQuery;
    }

    /**
     * Shows all the products
     * @return View
     */
    public function index(): View {
        return view('product.index');
    }

    /**
     * Show the form for creating a new product.
     * @return View
     */
    public function create(): View {
        return view('product.create');
    }

    /**
     * Store a newly created product in storage.
     *
     * @param BaseProductRequest $request
     * @return RedirectResponse
     */
    public function store(BaseProductRequest $request): RedirectResponse {
        $validatedData = $request->validated();
        $image = $request->file('product_image');
        $imageName = $validatedData['product_name'] . '-' . time() . '.' . $image->getClientOriginalExtension();
        Storage::disk('public')->putFileAs("/images/products/", $image, $imageName, 'public');

        //Creating Base Product
        $baseProduct = BaseProduct::create([
            'name' => $validatedData['product_name'],
            'mrp' => $validatedData['product_price'],
            'stock' => $validatedData['product_stock'],
            'description' => $validatedData['product_description'],
            'has_variant' => $validatedData['is_variant'],
            'image_url' => Storage::url("/images/products/" . $imageName),
            'created_by' => Auth::user()->id
        ]);


        //Storing variants of the product
        if ($validatedData['is_variant'] === self::VARIANT) {
            $variant_values_id = [];
            $variant_id = -1;
            for ($i = 0; $i < (int)$request->counter; $i++) {
                $optionName = $request['option_name_' . $i];
                $optionValue = $request['option_values_' . $i];

                //eliminate to duplicate variant values of a particular variant
                $optionValue = array_unique(array_change_key_case($optionValue, CASE_UPPER));
                //Creating Variant if it does not exist
                if (Variant::where('name', strtoupper($optionName))->exists()) {
                    $variant_id = Variant::where('name', strtoupper($optionName))->first()->id;
                } else {
                    $variant = Variant::create([
                        'name' => strtoupper($optionName),
                        'created_by' => Auth::user()->id
                    ]);
                    $variant_id = $variant->id;
                }

                //Creating Variant Values if it does not exist
                for ($j = 0; $j < count($optionValue); $j++) {
                    if (VariantValue::where('variant_id', $variant_id)->where('value', strtoupper($optionValue[$j]))->exists
                    ()) {
                        array_push($variant_values_id, VariantValue::where('value', strtoupper($optionValue[$j]))->first()->id);
                    } else {
                        $variant_value = VariantValue::create([
                            'variant_id' => $variant_id,
                            'value' => strtoupper($optionValue[$j]),
                            'created_by' => Auth::user()->id
                        ]);
                        array_push($variant_values_id, $variant_value->id);
                    }
                }
            }


            //Inserting into Product Variant to preserve product variant relationship
            for ($k = 0; $k < count($variant_values_id); $k++) {
                ProductVariant::create([
                    'base_product_id' => $baseProduct->id,
                    'variant_value_id' => $variant_values_id[$k],
                    'created_by' => Auth::user()->id
                ]);
            }

            $result = $this->productVariantQuery->getAllProductVariantWithValues($baseProduct->id)
                ->toArray();
            $sku = $this->skuGenerator->generate($baseProduct->name, $result);


            $productVariantPermute = array();
            foreach ($sku as $key => $value) {
                $productPermute = ProductPermute::create([
                    'base_product_id' => $baseProduct->id,
                    'sku' => $key,
                    'created_by' => Auth::user()->id
                ]);

                foreach ($value as $var) {
                     array_push($productVariantPermute, [
                         'product_id' => $productPermute->id,
                         'variant_value_id' => $var[1],
                         'created_by' => Auth::user()->id
                     ]);
                }
            }
            ProductVariantPermute::insert($productVariantPermute);
        }

        if ($validatedData['is_variant'] === self::VARIANT) {
            return redirect()->route('product.sku', [$baseProduct->id]);
        }
        return redirect()->route('product.index')->with('message', self::PRODUCT_CREATED_SUCCESSFULLY);
    }


    /**
     * Shows generated skus
     * @param int $id
     * @return View
     */
    public function productSku(int $id): View {
        $baseProduct = BaseProduct::findOrFail($id);
        //To dynamically populate columns in the datatables according to the variants of the product
        $productPermuteVariants = $this->productPermuteQuery->getAllProductVariants($baseProduct->id)->all();
        return view('product.view', ['productId' => $baseProduct->id, 'product_name' => $baseProduct->name, 'variants'
        =>
        $productPermuteVariants]);
    }


    /**
     * Fetches the data for datatable
     * @return mixed
     * @throws Exception
     */
    public function datatables() {
        $product = BaseProduct::all();

        return DataTables::of($product)
            ->editColumn('name', function ($product) {
                return ucfirst($product->name);
            })
            ->addColumn('image', function ($product) {
                return ViewHelper::controlImage($product->image_url);
            })
            ->editColumn("has_variant", function ($product) {
                if ($product->has_variant === BaseProduct::VARIANT) {
                    return ViewHelper::controlLinkButton('fa fa-eye', 'btn-primary', $product->id,
                        '/product/' . $product->id . '/sku', 'btn');
                }
                return "No Variants";
            })
            ->rawColumns(['has_variant', 'image'])
            ->make(true);
    }
}
