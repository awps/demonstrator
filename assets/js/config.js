;(function( $ ) {

	"use strict";

	$.fn.DEMONSTRATOR_FrontEnd = function( options ) {

		if (this.length > 1){
			this.each(function() {
				$(this).DEMONSTRATOR_FrontEnd(options);
			});
			return this;
		}

		// Defaults
		var settings = $.extend({}, options );

		// Cache current instance
		var plugin = this;

		//Plugin go!
		var init = function() {
			plugin.build();
		}

		// Build structure
		this.build = function() {
			var self = false;

			var _base = {

				prepareSite: function(){

					$( window ).on( 'resize', function() {
						self.setFrameDims();
					});

					self.setFrameDims();

					if (top.location != location) {
						top.location.href = document.location.href;
					}

					// Set menu labels
					// -----------------------
					var _theme_id = new Uri( window.location.href ).getQueryParamValue( 'theme' );
					var _style_id = new Uri( window.location.href ).getQueryParamValue( 'style' );

					if( _theme_id && demonstrator_themes[ _theme_id ] ){
						self.setMenuLabel( 'themes', _theme_id );
						
						if( _style_id ){
							self.setMenuLabel( 'styles', _theme_id, _style_id );
						}
						
						// Set the initial URL for "Purchase" button.
						self.setPurchaseUrl( _theme_id );
					}


				},

				setFrameDims: function( _without_bar ) {
					var topBarHeight = $( '#demonstrator-bar' ).height();
					
					if( _without_bar ){
						$( '.preview-frame' ).css({
							'height': $( window ).height() +'px',
						});
					}
					else{
						$( '.preview-frame' ).css({
							'height': $( window ).height() - 50 +'px',
							// 'top': topBarHeight +'px'
						});
					}
				},

				dropdowns: function(){
					$( '#menu-themes' ).on( 'click', function(){
						self.openDropdown( 'themes' );
					} );
					$( '#menu-styles' ).on( 'click', function(){
						self.openDropdown( 'styles' );
					} );
				},

				openDropdown: function( _id ){
					$( '#menu-' + _id ).addClass( 'active' ).removeClass( 'hidden' );
					$( '.demonstrator-dropdown.' + _id ).addClass( 'active' );
					
					if( _id === 'themes' ){
						self.closeDropdown( 'styles' );
					}
					else if( _id === 'styles' ){
						self.closeDropdown( 'themes' );
					}
				},

				closeDropdown: function( _id ){
					$( '#menu-' + _id ).removeClass( 'active' );
					$( '.demonstrator-dropdown.' + _id ).removeClass( 'active' );
				},

				closeDropdowns: function(){
					self.closeDropdown( 'themes' );
					self.closeDropdown( 'styles' );
				},

				hideAllStyles: function( _theme_id ){
					var _sel = _theme_id ? '.style-item:not(.'+ _theme_id +')' : '.style-item';
					$( _sel ).addClass( 'hidden' );
					$( '.style-item.' + _theme_id ).removeClass( 'hidden' );
				},

				loadThemeOnClick: function(){
					$( '.a-demo-item-link' ).on( 'click', function( event ){
						event.preventDefault();

						var _this = $(this),
						_theme_id = _this.data( 'theme-id' ),
						_style_id = _this.data( 'style-id' ),
						_mode = ( _this.hasClass( 'theme' ) ? 'themes' : 'styles' );

						// It's a Theme item
						if( 'themes' === _mode ){
							if( self.themeHasStyles( _theme_id ) ){
								self.hideAllStyles( _theme_id );
								self.openDropdown( 'styles' ); // Setup styles tab(and make visible)
								self.closeDropdowns(); // Hide styles container

							}
							else{
								$( '#menu-styles' ).removeClass( 'active' ).addClass( 'hidden' );
								self.closeDropdowns();
							}
							
							// Set theme(and style) menu name
							self.setThemeMenuLabel( _theme_id );
						}

						// It's a style
						else{
							self.setMenuLabel( _mode, _theme_id, _style_id );
						}

						// Set the URL for "Purchase" button
						self.setPurchaseUrl( _theme_id );

						// Finally load the URL
						self.loadUrl( _theme_id, _style_id );

					} );
				},

				// Return the style name if exists or false
				getThemeDefaultStyleId: function( _theme_id ){
					if( ! self.themeHasStyles( _theme_id ) )
						return false;

					var _style_id = false, 
					_theme   = demonstrator_themes[ _theme_id ],
					_styles;

					if( _theme !== undefined ){
						if( _theme.demo_url !== null && typeof _theme.demo_url === 'object' ){
							_styles = _theme.demo_url;
							
							// Get the first style ID
							_style_id = Object.keys( _styles )[0];

						}
					}

					return _style_id;
				},

				getThemeDemoUrl: function( _theme_id, _style_id ){
					var _url = false, 
					_theme   = demonstrator_themes[ _theme_id ],
					_demo_url,
					_styles;

					if( _theme !== undefined ){
						if( _theme.demo_url !== null && typeof _theme.demo_url === 'object' ){
							_styles = _theme.demo_url;
							
							// Get the first style ID
							if( _style_id === undefined  ){
								_style_id = Object.keys( _styles )[0];
							}

							_url = _styles[ _style_id ][ 'url' ];

						}
						else if( _theme.demo_url ){
							_url = _theme.demo_url;
						}
					}

					return _url;
				},

				getThemePurchaseUrl: function( _theme_id ){
					var _url = false, 
					_theme   = demonstrator_themes[ _theme_id ];

					if( _theme !== undefined ){
						if( _theme.short_buy_url ){
							_url = _theme.short_buy_url;
						}
						else if( _theme.purchase_url ){
							_url = _theme.purchase_url;
						}
					}

					return _url;
				},

				// Determine if a theme has styles or just a single demo URL
				// Return true if `demo_url` is an object.
				themeHasStyles: function( _theme_id ){
					var _has_styles = false, 
					_theme = demonstrator_themes[ _theme_id ];

					if( _theme !== undefined ){
						if( _theme.demo_url !== null && typeof _theme.demo_url === 'object' ){
							_has_styles = true;
						}
					}

					return _has_styles;
				},

				// Determine if a theme contains a style id
				// Return style data if exists or `false` if is not found.
				getThemeStyle: function( _theme_id, _style_id ){
					if( ! self.themeHasStyles( _theme_id ) )
						return false;

					var _style_data = false, 
					_theme = demonstrator_themes[ _theme_id ];

					if( _theme !== undefined ){
						if( _theme.demo_url !== null && typeof _theme.demo_url === 'object' ){
							var _styles = _theme.demo_url;
							
							if( _style_id && _styles[ _style_id ] ){
								_style_data = _styles[ _style_id ];
							}

						}
					}

					return _style_data;
				},

				// Get the name of selected theme
				getThemeName: function( _theme_id ){
					var _name = false,
					_theme = demonstrator_themes[ _theme_id ];

					if( _theme !== undefined ){
						_name = _theme.label;
					}

					return _name;
				},

				// Get the category of selected theme
				getThemeCategory: function( _theme_id ){
					var _category = false,
					_theme = demonstrator_themes[ _theme_id ];

					if( _theme !== undefined ){
						if( _theme.category !== undefined ){
							_category = _theme.category;
						}
					}

					return _category;
				},

				// Get the category of selected theme
				getThemeCategoryBadge: function( _theme_id ){
					var _category = self.getThemeCategory( _theme_id );

					if( _category ){
						_category = '<span class="category-badge">'+ _category +'</span>';
					}

					return _category;
				},

				loadUrl: function( _theme_id, _style_id ){
					var _theme_url = self.getThemeDemoUrl( _theme_id, _style_id ),
					close_dd = true, // Used to close dropdowns
					_new_url,
					_style_data;
					
					// Load url in iframe
					$( '#preview' ).attr( 'src', _theme_url );

					// TODO: Add loading indicator

					// Build the current Window URL with this theme ID
					_new_url  = new Uri( window.location.href ).replaceQueryParam( 'theme', _theme_id );
					
					if( self.getThemeStyle( _theme_id, _style_id ) ){
						_new_url  = new Uri( _new_url ).replaceQueryParam( 'style', _style_id );
					}
					else{
						_new_url  = new Uri( _new_url ).deleteQueryParam( 'style' );
						close_dd = false;
					}

					history.pushState('', 'Theme URL: ' + _new_url, _new_url);

					if( close_dd ){
						self.closeDropdowns();
					}
				},

				hideMenuWhenClickOutside: function(){
					$( document ).mouseup( function( event ) {
						var _menu_selector = $( '.menu-selector' );
						var _dd_selector = $( '.demonstrator-dropdown' );
						
						if ( 
							$('#menu-themes').hasClass( 'selected' ) &&
							// ! _menu_selector.is( event.target ) && 
							// _menu_selector.has( event.target ).length === 0 && 
							! _dd_selector.is( event.target ) && 
							_dd_selector.has( event.target ).length === 0
						){
							self.closeDropdowns();
						}
					});
				},

				setMenuLabel: function( _for, _theme_id, _style_id ){
					var _title;

					if( 'themes' === _for ){
						_title = 'Theme: ' + self.getThemeName( _theme_id ) + ' ' + self.getThemeCategoryBadge( _theme_id );
					}
					else if( 'styles' === _for ){
						var _style = self.getThemeStyle( _theme_id, _style_id );
						if( _style ){
							_title = 'Style: ' + _style.label;
						}
					}

					if( _title ){
						$( '.menu-selector.menu-'+ _for ).addClass('selected');
						$( '.menu-selector.menu-'+ _for +' .placeholder' ).html( _title );
					}
				},

				setThemeMenuLabel: function( _theme_id ){
					// Set theme menu name
					self.setMenuLabel( 'themes', _theme_id ); 

					// Set style menu name
					self.setMenuLabel( 'styles', _theme_id, self.getThemeDefaultStyleId( _theme_id ) );
				},

				setPurchaseUrl: function( _theme_id ){
					var _url = self.getThemePurchaseUrl( _theme_id );
					if( _url !== false ){
						$('#purchase').attr( 'href', _url ).removeClass( 'hidden' );
					}
					else{
						$('#purchase').attr( 'href', '#' ).addClass( 'hidden' );
					}
				},

				toggleBar: function(){
					$('#toggle-bar').on( 'click', function(){
						if( ! $('#menu-themes').hasClass( 'selected' ) )
							return;
					
						// Open bar
						if( $('#demonstrator-bar').hasClass('top-bar-closed') ){
							$('#demonstrator-bar').removeClass( 'top-bar-closed' );
							$('.preview-frame').removeClass( 'top-bar-closed' );
							self.setFrameDims();
						}

						// Close bar
						else{
							$('#demonstrator-bar').addClass( 'top-bar-closed' );
							$('.preview-frame').addClass( 'top-bar-closed' );
							self.setFrameDims( true );
						}

					} );
				},

				/*
				-------------------------------------------------------------------------------
				Construct plugin
				-------------------------------------------------------------------------------
				*/
				__construct: function(){
					self = this;

					self.prepareSite();
					self.dropdowns();
					self.loadThemeOnClick();
					self.hideMenuWhenClickOutside();
					self.toggleBar();

					return this;
				}

			};

			/*
			-------------------------------------------------------------------------------
			Rock it!
			-------------------------------------------------------------------------------
			*/
			_base.__construct();

		}

		//Plugin go!
		init();
		return this;

	};

	$( document ).ready( function(){
		$( 'body' ).DEMONSTRATOR_FrontEnd();
	} );

})(jQuery);