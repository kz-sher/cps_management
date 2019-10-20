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

// Ajax delete request function
function sendAjaxMultipleDeleteRequest(self, subCheckedBoxClass, targetTable, idStr){
    var allVals = [];   

    $(subCheckedBoxClass + ":checked").each(function() {  
        allVals.push($(this).attr('value'));
    });  

    if(allVals.length <= 0){  
        alert("Please select row.");  
    }  
    else {  

        var check = confirm("Are you sure you want to delete this row?");  
        if(check == true){  

            var join_selected_values = allVals.join(","); 
            console.log(join_selected_values);
            $.ajax({
                url: self.data('url'),
                type: 'DELETE',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: 'ids='+join_selected_values,
                success: function (data) {
                    if (data['success']) {
                        $(subCheckedBoxClass + ":checked").each(function() {  
                            $(this).parents("tr").remove();
                        });
                        alert(data['success']);

                        var rowCount = $(targetTable + ' tr').length;
                        console.log(rowCount);
                        if(rowCount === 1){
                            console.log(targetTable);
                            $(targetTable).find('tbody')
                                .append("<tr><td colspan='100%'>No "+ idStr +" found</td></tr>");
                        } 

                    } else if (data['error']) {
                        alert(data['error']);
                    } else {
                        alert('Whoops Something went wrong!!');
                    }
                },
                error: function (data) {
                    alert(data.responseText);
                }
            });

          $.each(allVals, function( index, value ) {
              $(targetTable + ' tr').filter("[data-row-id='" + value + "']").remove();
          });

        }  
    }
}