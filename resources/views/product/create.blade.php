@extends('layouts.app')
@push('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
@endpush
@section('content')
    @include('layouts.headers.cards')

    <div class="container-fluid mt--7">
        <div class="row">
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
                        <form action={{route('product.store')}} method="POST" id="add-product"
                              enctype='multipart/form-data'>
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="productName">Product Name <label
                                                        class="text-danger">*</label></label>
                                                <input type="text" class="form-control"
                                                       name="product_name" id="product_name"
                                                       required
                                                       value="{{ old('product_name') }}"
                                                       placeholder="Enter Product Name">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="productName">Product Price <label
                                                        class="text-danger">*</label></label>
                                                <input type="text" class="form-control" name="product_price"
                                                       id="product-price"
                                                       required
                                                       value="{{ old('product_price') }}"
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
                                                          ('product_description') }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group d-flex flex-column">
                                                <label for="productName">Upload Product Image <label
                                                        class="text-danger">*</label></label>
                                                <input type="file"
                                                       required
                                                       id="product-image" name="product_image"
                                                       accept="image/png, image/jpeg">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="custom-control custom-radio mb-3">
                                            <input name="is_variant" class="custom-control-input"
                                                   id="customRadio5"
                                                   type="radio" value="0"
                                                   @if(old('is_variant') === "0")  checked @endIf>
                                            <label class="custom-control-label" for="customRadio5">No this Item
                                                does not have options</label>
                                        </div>

                                        <div class="custom-control custom-radio mb-3">
                                            <input name="is_variant" class="custom-control-input" id="customRadio6"
                                                   type="radio" value="1"
                                                   @if(old('is_variant') === "1")  checked @endIf>
                                            <label class="custom-control-label" for="customRadio6">Yes, this
                                                item has options.</label>
                                        </div>

                                    </div>
                                    <div class="stock">
                                    </div>
                                    <div class="option  ">
                                        <div class="container ">
                                            <div class="row">
                                                <div class="col-4">
                                                    <label for="">Variant Name</label>
                                                </div>
                                                <div class="col-4">
                                                    <label for="">Variant Value</label>
                                                </div>
                                            </div>
                                            <div class="option-content">

                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-2 col-xl-offset-2">
                                                    <button class="btn btn-primary" id="add-option"
                                                            type="button">Add Option
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="col-2">
                                        <div class="loader d-none">
                                            <button class="btn btn-primary mb-2" type="button" disabled>
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                      aria-hidden="true"></span>
                                                Loading...
                                            </button>
                                        </div>
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
    <script>
        let oldProductStock = '';
        @if (old('product_stock'))
            oldProductStock = "{{old('product_stock')}}"
        @endIf
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script src="{{ asset('js/product/create.js') }}"></script>
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>
@endpush
