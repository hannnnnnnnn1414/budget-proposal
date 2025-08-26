$(document).ready(function () {
    var currentMonth = new Date().getMonth() + 1;
    var currentYear = new Date().getFullYear();

    $("#filterMonth").val(currentMonth);
    $("#filterYear").val(currentYear);

    $("#example").DataTable({
        paging: false,
        info: false,
        searching: true,
    });

    $("#example_filter").appendTo("#tableHeader").addClass("ms-auto");
});
