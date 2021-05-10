$(document).ready(function() {
    let counter = 0;
    $('.option').hide()

    $("#add-product").submit(function () {
        $(".loader").addClass('d-block')
        $(".loader").removeClass('d-none')
        $(".loader-submit").addClass('d-none')
        $(".loader-submit").removeClass('d-block')

        $("<input type='hidden' />").attr("name", "counter").attr("value", counter).appendTo("#add-product");
        return true
    })

    const stock = `<div class="col-6">
                        <div class="form-group">
                            <label for="productName">Product Stock <label
                                    class="text-danger">*</label> </label>
                            <input type="text" class="form-control" name="product_stock"
                                   id="product_stock"
                                   required
                                   value="${oldProductStock}"
                                   placeholder="Enter Product stock">
                        </div>
                    </div>`
    initializeSelect2()
    const  addSelect2 = (counter)  =>  `<div class="row mt-3 remove-${counter}" >
                                               <div class="col-4">
                                                   <input type="text" class="form-control" name="option_name_${counter}"
                                                       id="option_name"
                                                       required
                                                       placeholder="Enter Option Name">
                                               </div>
                                               <div class="col-6">
                                                   <select class="option_value form-control" multiple
                                                           id="option_value"
                                                           required
                                                           name="option_values_${counter}[]">
                                                   </select>
                                               </div>
                                                <div class="col-2 ">
                                                   <button class="btn btn-primary remove-option-${counter}" id="remove-option"
                                                           type="button">X</button>
                                               </div>
                                           </div>`

    $("#add-option").click(function () {
        $('.option-content').append(addSelect2(counter++));
        initializeSelect2()
    })

    $(`.option-content`).on("click", `#remove-option`, (function () {
        counter--;
        if (counter === 0) {
            $('.option').hide()
            $("input[name$='is_variant']").prop('checked', false); // Unchecks it
        }
        $(this).parent().parent().remove()
    }))
    $("input[name$='is_variant']").click(function () {
        if ($("input:radio[name='is_variant']:checked").val() === "1") {
            $('.option').show()
            $('.option-content').append(addSelect2(counter++));
            $('.stock').empty()
            initializeSelect2()
        } else  {
            $('.option').hide()
            counter = 0;
            $('.stock').html(stock);
            $('.option-content').empty()

        }
    });
})


function initializeSelect2() {
    $('.option_value').select2({
        placeholder: 'Type option values',
        width: "100%",
        tags: true,
        tokenSeparators: [' ']
    })
}
