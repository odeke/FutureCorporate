
(function($) {
	$(document).ready(function() {	
		// jQuery-UI Dialog
        if( $.fn.dialog ) {
            $("#mws-form-dialog").dialog({
                autoOpen: false,
                title: "Change selected currency",
                modal: true,
                width: "640",
                buttons: [{
                    text: "Submit",
                    click: function () {
                        $("#change_currency").submit();
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
        
		// Collapsible Panels
		$( '.mws-panel.mws-collapsible' ).each(function(i, element) {
			var p = $( element ),	
				header = p.find( '.mws-panel-header' );

			if( header && header.length) {
				var btn = $('<div class="mws-collapse-button mws-inset"><span></span></div>').appendTo(header);
				$('span', btn).on( 'click', function(e) {
					var p = $( this ).parents( '.mws-panel' );
					if( p.hasClass('mws-collapsed') ) {
						p.removeClass( 'mws-collapsed' )
							.children( '.mws-panel-inner-wrap' ).hide().slideDown( 250 );
					} else {
						p.children( '.mws-panel-inner-wrap' ).slideUp( 250, function() {
							p.addClass( 'mws-collapsed' );
						});
					}
					e.preventDefault();
				});
			}

			if( !p.children( '.mws-panel-inner-wrap' ).length ) {
				p.children( ':not(.mws-panel-header)' )
					.wrapAll( $('<div></div>').addClass( 'mws-panel-inner-wrap' ) );
			}
		})
	
		/* Side dropdown menu */
		$("div#mws-navigation ul li a, div#mws-navigation ul li span")
			.on('click', function(event) {
				if(!!$(this).next('ul').length) {
					$(this).next('ul').slideToggle('fast', function() {
						$(this).toggleClass('closed');
					});
					event.preventDefault();
				}
			});
		
		/* Responsive Layout Script */
		$("#mws-nav-collapse").on('click', function(e) {
			$( '#mws-navigation > ul' ).slideToggle( 'normal', function() {
				$(this).css('display', '').parent().toggleClass('toggled');
			});
			e.preventDefault();
		});
		
		/* Form Messages */
		$(".mws-form-message").on("click", function() {
			$(this).animate({ opacity:0 }, function() {
				$(this).slideUp("normal", function() {
					$(this).css("opacity", '');
				});
			});
		});

		// Checkable Tables
		$( 'table thead th.checkbox-column :checkbox' ).on('change', function() {
			var checked = $( this ).prop( 'checked' );
			$( this ).parents('table').children('tbody').each(function(i, tbody) {
				$(tbody).find('.checkbox-column').each(function(j, cb) {
					$( ':checkbox', $(cb) ).prop( "checked", checked ).trigger('change');
				});
			});
		});

		// Bootstrap Dropdown Workaround
		$(document).on('touchstart.dropdown.data-api', '.dropdown-menu', function (e) { e.stopPropagation() });
		
		/* File Input Styling */
		$.fn.fileInput && $("input[type='file']").fileInput();

		// Placeholders
		$.fn.placeholder && $('[placeholder]').placeholder();

		// Tooltips
		$.fn.tooltip && $('[rel="tooltip"]').tooltip();

		// Popovers
		$.fn.popover && $('[rel="popover"]').popover();
	});
}) (jQuery);