/*
 * MWS Admin v2.1 - Wizard Demo JS
 * This file is part of MWS Admin, an Admin template build for sale at ThemeForest.
 * All copyright to this file is hold by Mairel Theafila <maimairel@yahoo.com> a.k.a nagaemas on ThemeForest.
 * Last Updated:
 * December 08, 2012
 *
 */

;(function( $, window, document, undefined ) {

    $(document).ready(function() {
        var theme = 'fresh';
        // prepare the data
        var source =
        {
            datatype: "json",
            datafields: [
                { name: 'bureaus_id'},
                { name: 'bureau'},
                { name: 'buy_USD'},
                { name: 'sell_USD'},
                { name: 'buy_TZ'},
                { name: 'sell_TZ'},
                { name: 'buy_KSH'},
                { name: 'sell_KSH'},
                { name: 'buy_EURO'},
                { name: 'sell_EURO'},
                { name: 'buy_GBP'},
                { name: 'sell_GBP'},
                { name: 'buy_SDG'},
                { name: 'sell_SDG'},
                { name: 'buy_RWF'},
                { name: 'sell_RWF'},
                { name: 'buy_ZAR'},
                { name: 'sell_ZAR'},
                { name: 'buy_INR'},
                { name: 'sell_INR'},
                { name: 'buy_JPY'},
                { name: 'sell_JPY'},
                { name: 'buy_CHF'},
                { name: 'sell_CHF'},
            ],
            id: 'bureaus_id',
            async: false,
            url: 'http://inforexafrica.com/app/data.php'
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        //bureaus details
        var detailsSource =
        {
            datafields: [
                { name: 'bureaus_id' },
                { name: 'officephone' },
                { name: 'cellphone' },
                { name: 'email' },
                { name: 'street' },
                { name: 'city' },
                { name: 'premises' }
            ],
            datatype: "json",
            url: 'http://inforexafrica.com/app/details.php',
            async: false
        };

        var detailsDataAdapter = new $.jqx.dataAdapter(detailsSource, { autoBind: true });
        details = detailsDataAdapter.records;

        // create nested grid.
        var officephone1,cellphone1,email1,street1,city1,premises1;
        var initrowdetails = function (index, parentElement, gridElement, record) {
            var id = record.uid.toString();
            var grid = $($(parentElement).children()[0]);

            var filtergroup = new $.jqx.filter();
            var filter_or_operator = 1;
            var filtervalue = id;
            var filtercondition = 'equal';
            var filter = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
            // fill the orders depending on the id.
            var detailsbyid = [];
            for (var m = 0; m < details.length; m++) {
                var result = filter.evaluate(details[m]["bureaus_id"]);
                if (result)
                    detailsbyid.push(details[m]);
            }

            var detailssource = { datafields: [
                { name: 'bureaus_id' },
                { name: 'officephone' },
                { name: 'cellphone' },
                { name: 'email' },
                { name: 'street' },
                { name: 'city' },
                { name: 'premises' }
            ],
                id: 'bureaus_id',
                localdata: detailsbyid
            }

            if (grid != null) {
                var photorenderer = function (row, column, value) {
                    var name = $('#jqxgrid').jqxGrid('getrowdata', row).bureaus_id;
                    var imgurl = 'images/bureaus/' + name.toLowerCase() + '.jpg';
                    var img = '<div style="background: white;"><img style="margin:0px; margin-left: 0px;" width="360" height="150" src="' + imgurl + '"></div>';
                    return img;
                }
                grid.jqxGrid({
                    source: detailssource,
                    theme: 'classic',
                    width: 860,
                    height: 100,
                    columns: [
                        { text: 'Office No.', datafield: 'officephone', width: 200 },
                        { text: 'Cell', datafield: 'cellphone', width: 200 },
                        { text: 'Email', datafield: 'email', width: 150 },
                        { text: 'Street', datafield: 'street', width: 150 },
                        { text: 'City', datafield: 'city', width: 150 },
                        { text: 'Premises', datafield: 'premises', width: 200 }
                    ]
                });
            }
        }



        var renderer = function (row, column, value) {
            return '<span style="margin-left: 4px; margin-top: 9px; float: left;">' + value + '</span>';
        }

        var detailsTemplate = "<div id='grid' style='margin: 10px;'></div>";

        $("#jqxgrid").jqxGrid(
            {
                width: 920,
                height: 420,
                source: source,
                theme: theme,
                rowdetails: true,
                sortable: true,
                pageable: true,
                rowsheight: 35,
                initrowdetails: initrowdetails,
                rowdetailstemplate: { rowdetails: detailsTemplate, rowdetailsheight: 220, rowdetailshidden:true },
                ready: function () {
                    $("#jqxgrid").jqxGrid('showrowdetails',-1);
                },
                columns: [
                    { text: 'Bureaus Name', datafield: 'bureau', width: 250, pinned: true },
                    { text: 'USD | Buying', datafield: 'buy_USD', width: 100,cellsformat:'c2',cellsalign:'right', cellsrenderer: renderer },
                    { text: 'USD | Selling', datafield: 'sell_USD', width: 100,cellsformat:'c2',cellsalign:'right', cellsrenderer: renderer },
                    { text: 'GBP | Buying', datafield: 'buy_GBP', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'GBP | Selling', datafield: 'sell_GBP', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'Euro | Buying', datafield: 'buy_EURO', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'Euro | Selling', datafield: 'sell_EURO', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'Ksh | Buying', datafield: 'buy_KSH', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'Ksh | Selling', datafield: 'sell_KSH', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'Tz | Buying', datafield: 'buy_TZ', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'Tz | Selling', datafield: 'sell_TZ', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'SDG | Buying', datafield: 'buy_SDG', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'SDG | Selling', datafield: 'sell_SDG', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'RWF | Buying', datafield: 'buy_RWF', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'RWF | Selling', datafield: 'sell_RWF', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'ZAR | Buying', datafield: 'buy_ZAR', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'ZAR | Selling', datafield: 'sell_ZAR', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'INR | Buying', datafield: 'buy_INR', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'INR | Selling', datafield: 'sell_INR', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'JPY | Buying', datafield: 'buy_JPY', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'JPY | Selling', datafield: 'sell_JPY', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'CHF | Buying', datafield: 'buy_CHF', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                    { text: 'CHF | Selling', datafield: 'sell_CHF', width: 100,cellsformat:'c2', cellsalign:'right', cellsrenderer: renderer },
                ]
            });

        // init buttons.
        $("#refresh").jqxButton({ theme: theme });
        $("#refresh").click(function () {
            $("#jqxgrid").jqxGrid('updatebounddata');
        });

        // column selection
        var listSource = [{ label: 'USD', value: 'USD', checked: true },
            { label: 'GBP', value: 'GBP', checked: true },
            { label: 'Euro', value: 'EURO', checked: true },
            { label: 'Ksh', value: 'KSH', checked: true },
            { label: 'Tz', value: 'TZ', checked: true },
            { label: 'SDG', value: 'SDG', checked: true },
            { label: 'RWF', value: 'RWF', checked: true },
            { label: 'ZAR', value: 'ZAR', checked: true },
            { label: 'INR', value: 'INR', checked: true },
            { label: 'JPY', value: 'JPY', checked: true },
            { label: 'CHF', value: 'CHF', checked: true}];

        // Create a jqxDropDownList
        $("#jqxDropDownList").jqxDropDownList({ checkboxes: true, source: listSource, displayMember: "label", valueMember: "value", width: 400, height: 25, theme: theme, promptText: 'Select Currencies:' });
        $("#jqxDropDownList").jqxDropDownList('checkIndex', 0);
        // bind to the checkChange event.
        $("#jqxDropDownList").bind('checkChange', function (event) {
            if (event.args.checked) {
                $("#jqxgrid").jqxGrid('showcolumn', 'buy_'+event.args.value);
                $("#jqxgrid").jqxGrid('showcolumn', 'sell_'+event.args.value);
            }
            else {
                $("#jqxgrid").jqxGrid('hidecolumn', 'buy_'+event.args.value);
                $("#jqxgrid").jqxGrid('hidecolumn', 'sell_'+event.args.value);
            }
        });

    });

}) (jQuery, window, document);