$(document).ready(function(){
    // Bulk options select all checkboxes
    $('#selectAllCheckboxes').click(function(){
        if (this.checked) {
            $('.selectCheckbox').each(function(){ this.checked = true; });
        } else {
            $('.selectCheckbox').each(function(){ this.checked = false; });
        }
    });

    // Clicking table row header orders by that column
    $('.table__headers th').not(".clickable_th").click(function() {
        var selected_header = $(this);
        var basename = getPageName(window.location.pathname) + ".php";

        if (selected_header.hasClass("ASC")) {
            // Order loop has finished, remove all filtering and redirect user to default posts page
            var href = basename;
            if (href) window.location = href;
            return;
        } else if (selected_header.hasClass("DESC")) {
            // Order is already set to descending, change to ascending
            var order_direction = "ASC";
        } else {
            // No order set yet, set to descending
            var order_direction = "DESC";
        }
        
        order_by = selected_header.attr("rel");

        // Redirect user with get request
        var href = basename + "?source=view_all&orderby=" + order_by + "&orderdirection=" + order_direction;
        if (href) window.location = href;
    });
});


function getPageName(url) {
    var index = url.lastIndexOf("/") + 1;
    var filenameWithExtension = url.substr(index);
    var filename = filenameWithExtension.split(".")[0]; // <-- added this line
    return filename;                                    // <-- added this line
}