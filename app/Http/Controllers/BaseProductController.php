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
use App\Queries\BaseProductQuery;
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
    protected BaseProductQuery $baseProductQuery;

    /**
     * BaseProductController constructor.
     * @param SKUGenerator $skuGenerator
     * @param ProductVariantQuery $productVariantQuery
     * @param ProductPermuteQuery $productPermuteQuery
     * @param BaseProductQuery $baseProductQuery
     */
    public function __construct(SKUGenerator $skuGenerator, ProductVariantQuery $productVariantQuery, ProductPermuteQuery $productPermuteQuery, BaseProductQuery $baseProductQuery) {
        $this->skuGenerator = $skuGenerator;
        $this->productVariantQuery = $productVariantQuery;
        $this->productPermuteQuery = $productPermuteQuery;
        $this->baseProductQuery = $baseProductQuery;
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
        $createdById = Auth::user()->id;
        $validatedData = $request->validated();
        $image = $request->file('product_image');
        $imageName = $validatedData['product_name'] . '-' . time() . '.' . $image->getClientOriginalExtension();
        Storage::disk('public')->putFileAs("/images/products/", $image, $imageName, 'public');

        //Creating Base Product
        $baseProduct = BaseProduct::create([
            'name' => preg_replace("/\s+/", "", $validatedData['product_name']),
            'price' => $validatedData['product_price'],
            'description' => $validatedData['product_description'],
            'has_variant' => $validatedData['is_variant'],
            'image_url' => Storage::url("/images/products/" . $imageName),
            'created_by' => $createdById
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
                        'name' => preg_replace("/\s+/", "",strtoupper($optionName)),
                        'created_by' => $createdById
                    ]);
                    $variant_id = $variant->id;
                }

                //Creating Variant Values if it does not exist
                for ($j = 0; $j < count($optionValue); $j++) {
                    if (VariantValue::where('variant_id', $variant_id)->where('value', strtoupper($optionValue[$j]))
                        ->exists()) {
                        array_push($variant_values_id, VariantValue::where('variant_id', $variant_id)->where('value',
                                strtoupper($optionValue[$j]))->first()->id);
                    } else {
                        $variant_value = VariantValue::create([
                            'variant_id' => $variant_id,
                            'value' => strtoupper($optionValue[$j]),
                            'created_by' => $createdById
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
                    'created_by' => $createdById
                ]);
            }

            $result = $this->productVariantQuery->getAllProductVariantWithValues($baseProduct->id)
                ->toArray();
            $sku = $this->skuGenerator->generate($baseProduct->name, $result);


            $productVariantPermute = array();
            foreach ($sku as $key => $value) {
                $productPermute = ProductPermute::create([
                    'base_product_id' => $baseProduct->id,
                    'price' => $baseProduct->price,
                    'sku' => $key,
                    'created_by' => $createdById
                ]);

                foreach ($value as $var) {
                     array_push($productVariantPermute, [
                         'product_id' => $productPermute->id,
                         'variant_value_id' => $var[1],
                         'created_by' => $createdById
                     ]);
                }
            }
            ProductVariantPermute::insert($productVariantPermute);
        } else {
            $validateStock = $request->validate(
                ['product_stock' => 'required|integer']);
            ProductPermute::create([
                'base_product_id' => $baseProduct->id,
                'price' => $baseProduct->price,
                'sku' => $baseProduct->name,
                'stock' => $validateStock['product_stock'],
                'created_by' => $createdById
            ]);
        }

        if ($validatedData['is_variant'] === self::VARIANT) {
            return redirect()->route('product.sku.index', [$baseProduct->id]);
        }
        return redirect()->route('product.index')->with('message', self::PRODUCT_CREATED_SUCCESSFULLY);
    }

    /**
     * Added destroy function for product
     * @param BaseProduct $baseProduct
     * @return RedirectResponse
     */
    public function destroy(BaseProduct $baseProduct): RedirectResponse {
        ProductVariant::where('base_product_id', $baseProduct->id)->delete();

        $productPermuteIds = ProductPermute::where('base_product_id', $baseProduct->id)->pluck('id');
        ProductVariantPermute::whereIn('product_id', $productPermuteIds)->delete();
        ProductPermute::where('base_product_id', $baseProduct->id)->delete();

        $baseProduct->delete();


        return redirect()->back()->with('message', self::PRODUCT_CREATED_SUCCESSFULLY);
    }


    /**
     * Fetches the data for datatable
     * @return mixed
     * @throws Exception
     */
    public function datatables() {
        $product = $this->baseProductQuery->getAllBaseProductsWithStock();
        $productIds = $product->pluck('id');

        return DataTables::of($product)
            ->editColumn('name', function ($product) {
                return ucfirst($product->name);
            })
            ->addColumn('image', function ($product) {
                return ViewHelper::controlImage($product->image_url);
            })
            ->addColumn("delete", function ($product) {
                return ViewHelper::controlModalButton("fa fa-trash-alt", "btn-danger", $product->id, "delete", "deleteModal");
            })
            ->addColumn("stock", function ($product) {
                return $product->productPermutes()->whereNull('deleted_at')->sum('stock');
            })
            ->editColumn("has_variant", function ($product) use ($productIds) {
                if ($product->has_variant === BaseProduct::VARIANT) {
                    return ViewHelper::controlLinkButton('fa fa-eye', 'btn-primary', $product->id,
                        '/product/' . $product->id . '/sku', 'btn');
                }
                return  ViewHelper::controlText('Product Cannot have variants');
            })
            ->rawColumns(['has_variant', 'image', 'delete'])
            ->make(true);
    }
}
