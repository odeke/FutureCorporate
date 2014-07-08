;(function( $, window, document, undefined ) {

    $(document).ready(function() {

        // Data Tables
        if( $.fn.dataTable ) {
            $(".mws-datatable").dataTable();
            $(".mws-datatable-fn").dataTable({
                sPaginationType: "full_numbers"
            });
            $(".mws-datatable-bids").dataTable({
                "bFilter": false,
                "bPaginate": false,
                "aoColumns": [
                    null,
                    { "bSortable": false },
                    null,
                    { "bSortable": false }
                ]
            });
            $(".mws-datatable-bureaus").dataTable({
                "bFilter": true,
                sPaginationType: "full_numbers",
                "aoColumns": [
                    null,
                    { "bSortable": false },
                    null,
                    null,
                    { "bSortable": false }
                ]
            });

        }


        // jQuery-UI Dialog
        if( $.fn.dialog ) {

            $("#mws-form-dialog").dialog({
                autoOpen: false,
                title: "Create new ticket",
                modal: true,
                width: "640",
                buttons: [{
                    text: "Send",
                    click: function () {
                        $(this).find('form#mws-validate').submit();
                    }
                }]
            });
            $("#bureau_details-dialog").dialog({
                autoOpen: false,
                title: "Forex Bureau details",
                modal: true,
                width: "640",
                buttons: [{
                    text: "Close Dialog",
                    click: function () {
                        $(this).dialog("close");
                    }
                }]
            });

            $("#mws-form-dialog-mdl-btn").bind("click", function (event) {
                $("#mws-form-dialog").dialog("option", {
                    modal: true
                }).dialog("open");
                event.preventDefault();
            });
        }

        // Validation
        if( $.validator ) {
            $("#mws-validate").validate({
                rules: {
                    spinner: {
                        required: true,
                        max: 5
                    }
                },
                invalidHandler: function (form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        var message = errors == 1 ? 'You missed 1 field. It has been highlighted' : 'You missed ' + errors + ' fields. They have been highlighted';
                        $("#mws-validate-error").html(message).show();
                    } else {
                        $("#mws-validate-error").hide();
                    }
                }
            });
        }

    });

}) (jQuery, window, document);