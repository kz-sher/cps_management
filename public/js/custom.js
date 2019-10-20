// Full screen loading effect
$('.full-spinner-loader').click(function(){
    console.log('asd');
    $('#full-spinner-overlay').css('display','flex');
});

// Alert auto close
window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 4000);