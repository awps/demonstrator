;(function ( $ ) {

	$.fn.smk_Toolkit_Tabs = function( options ) {
		
		// Defaults
		var settings = $.extend({
			menuClass: '.smk_nav',
			menuItem: '.nav_item a',
			tabContentClass: '.tab_content',
		}, options );

		// Reffer to current instance
		var plugin = this;

		//"Constructor"
		var init = function() {
			plugin.tabsClick();
		}

		// Action on click
		this.tabsClick = function() {
			$(settings.menuClass).on('click', settings.menuItem, function(e){
				e.preventDefault();

				//Get the id
				var the_id = $(this).data('id');

				//Add class to current menu item
				$(settings.menuItem).not(this).removeClass('active');
				$(this).addClass('active');
				
				//Add class to current content
				$(settings.tabContentClass).not('#' + the_id).removeClass('active');
				$('#' + the_id).addClass('active');

			});
		}

		//"Constructor" init
		init();
		return this;

	}

}( jQuery ));