$(function() {

    let myColumns = [];
    variants.forEach((variant) => {
        myColumns.push({title: variant.name, data: variant.name})
    })

    var productTable = $('#product-skus');
    productTable.DataTable({
        processing: true,
        serverSide: true,
        ajax:  `/product/${skuId}/sku/datatables`,
        columns: [
            {title: "SKU", data: 'sku'},
            ...myColumns,
            {title: "Delete", data: 'delete'},

        ]
    });

    productTable.on('click', '.delete', function(e) {
        $id = $(this).attr('id');
        $('#delete_form').attr('action', '/sku/' + $id);
    });
})
