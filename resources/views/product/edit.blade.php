@extends('layouts.app')
@push('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
@endpush
@section('content')
    @include('layouts.headers.cards')

    <div class="container-fluid mt--8">
        <div class="row">
            <div class="col-6">
                <div class="card border-0 bg-transparent" >
                    <h4 class="text-white font-weight-light mb-0">Product Name</h4>
                    <h1 class="text-white">{{$baseProduct->name}}</h1>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-0 bg-transparent" >
                    <h4 class="text-white font-weight-light mb-0">Product Price</h4>
                    <h1 class="text-white">{{$baseProduct->price}}</h1>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action={{route('product.sku.update', [ $baseProduct, $productPermute])}} method="POST"
                              id="edit-product-sku"
                              enctype='multipart/form-data'>
                            {{ csrf_field() }}
                            @method("PUT")
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="productName">Product SKU Stock <label
                                                        class="text-danger">*</label> </label>
                                                <input type="text" class="form-control" name="product_stock"
                                                       id="product_stock"
                                                       required
                                                       value="{{old('product_stock', $productPermute->stock)}}"
                                                       placeholder="Enter Product stock">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="productName">Product SKU Price Difference <label
                                                        class="text-danger">*</label></label>
                                                <input type="text" class="form-control" name="product_price"
                                                       id="product-price"
                                                       required
                                                       value="{{ old('product_price', ($productPermute->price -
                                                       $baseProduct->price)) }}"
                                                       placeholder="Enter Product price">
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="productName">Product Description <label
                                                        class="text-danger">*</label></label>
                                                <textarea type="text" class="form-control" name="product_description"
                                                          id="product_description"
                                                          rows="1"
                                                          required
                                                          placeholder="Enter Product description">{{ old
                                                          ('product_description', $productPermute->description)
                                                          }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group d-flex flex-column">
                                                <label for="productName">Upload Product Image <label
                                                        class="text-danger">*</label></label>

                                                <div>
                                                    @if($productPermute->image_url)
                                                        <img src="{{$productPermute->image_url}}"
                                                             class="img img-thumbnail w-25"
                                                             alt="Could Not load
                                                        image">
                                                     @endif
                                                    <input type="file"
                                                           @if(!$productPermute->image_url)
                                                           required
                                                           @endif
                                                           id="product-image" name="product_image"
                                                           accept="image/png, image/jpeg">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="col-2">
                                        <div class="loader-submit d-block">
                                            <button class="btn btn-primary" id="create-product"
                                                    type="submit">Submit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth')
    </div>
@endsection

@push('js')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>
@endpush
