@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
@endpush

@section('content')
    @include('layouts.headers.cards')


    <div class="container-fluid mt--8">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 bg-transparent" >
                    <h4 class="text-white font-weight-light mb-0">Product Name</h4>
                    <h1 class="text-white">{{$product_name}}</h1>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-xl-12 mb-5 mb-xl-0">
                <div class="card shadow p-3">

                    <div class="table-responsive">
                        <!-- Projects table -->
                        <table class="table align-items-center table-flush" id="product-skus">
                            <thead class="thead-light">
                            <tr>
                            </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>

        </div>

        @include('layouts.footers.auth')
    </div>

    @component('includes.confirm-modal', [
        "id" => "deleteModal",
        "title" => "Delete Product SKU",
        "message" => "Are you sure you want to delete the SKU?",
        "path" => "/sku/". $productId,
    ])
        @slot('method')
            @method('DELETE')
        @endslot
    @endcomponent


@endsection

@push('js')


    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

    <script>
        let skuId = {!!$productId !!};
        let variants = {!!json_encode($variants)!!}
    </script>
    <script src="{{ asset('js/product/view.js') }}"></script>
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>
@endpush
