// Detect table row click that checks the corresponding checkbox
$('tbody tr').on('click', function(e) {
    if ($(event.target).is('a') || $(event.target).is('i') || $(event.target).is('input')){
         return;
    }
    var row_checkbox = $(this).find("input[type='checkbox']");
    if(row_checkbox.prop('checked') == true) {
        row_checkbox.prop('checked', false);
    }
    else{
        row_checkbox.prop('checked', true);
    }
});

// Full screen loading effect
$('.full-spinner-loader').click(function(){
    $('#full-spinner-overlay').css('display','flex');
});

// Alert auto close
window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 4000);

