$(function() {
    var productTable = $('#products');
    productTable.DataTable({
        processing: true,
        serverSide: true,
        ajax: 'product/datatables',
        columns: [
            {title: 'name', data: 'name', name: 'name'},
            {title: 'stock',data: 'stock', name: 'stock'},
            {title: 'mrp',data: 'mrp', name:'mrp'},
            {title: 'description',data: 'description', name: 'description'},
            {title: 'image', data: 'image', name: 'image'},
            {title: 'Show Variants', data: 'has_variant', name: 'has_variant'},
        ]
    });

    productTable.on('click', '.delete', function(e) {
        $id = $(this).attr('id');
        $('#delete_form').attr('action', '/product/datatables' + $id);
    });
})
