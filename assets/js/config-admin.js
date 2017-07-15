;(function( $ ) {

	"use strict";

	$.fn.DEMONSTRATOR_Plugin = function( options ) {

		if (this.length > 1){
			this.each(function() {
				$(this).DEMONSTRATOR_Plugin(options);
			});
			return this;
		}

		// Defaults
		var settings = $.extend({
			multiple: false,
		}, options );

		// Cache current instance
		var plugin = this;

		//Plugin go!
		var init = function() {
			plugin.build();
		}

		// Build structure
		this.build = function() {
			var self = false;
			var frame,
			_uploader;

			var _base = {

				openFrame: function(){
					$('body').on( 'click', '.demonstrator_uploader_btn', function( event ){
						event.preventDefault();

						/* _this button
						--------------------*/
						var _this = $(this);
						
						_uploader = _this.parents( '.demonstrator_uploader' );

						// console.log( _uploader );

						// This uploader frame has been created open it
						if( frame){
							frame.open();
						}
						else{
							frame = self.createMediaFrame();
							frame.open();
						}

						// console.log( frame );

						// When an image is selected in the media frame...
						frame.on( 'select', function() {

							// Get media attachment details from the frame state
							var attachments = frame.state().get('selection').toJSON();
							
							// console.log( attachments );

							/* Set media
							-----------------*/
							var _container = _uploader.find( '.media-container' ),
							_image = ( attachments[0] ) ? attachments[0] : false;
							if( _image && _image.url ){
								var _thumb = false;
								if( _image.sizes.large ){
									_thumb = _image.sizes.large.url;
								}
								else{
									_thumb = _image.url;
								}
								_container.html( '<img src="'+ _thumb +'" />' );
								_uploader.find( '.media-input' ).val( _thumb ).trigger('change');
							}

						});

					});
				},

				// Create a media frame
				createMediaFrame: function(){

					var _defaults = {
						frame: 'select',
						// title: _uploader.data('frame-title'),
						// button: {
						// 	text: _uploader.data('frame-button-label'),
						// },
						multiple: false
					};

					_defaults.library = {
						type: 'image'
					};

					return wp.media( _defaults );
				},

				uniqid: function(_nr, _underscore){
					
					var possible_letters = "abcdefghijklmnopqrstuvwxyz",
						possible_nr = "0123456789",
						possible_all = "abcdefghijklmnopqrstuvwxyz0123456789",
						_new_nr, text="", text1="", text2="", text3="";

					if( _nr < 4 ) _nr = 4;

					if( _underscore ){
						_new_nr = parseInt(_nr, 10) - 2;
						for( var i=0; i < 2; i++ ){
							text1 += possible_letters.charAt(Math.floor(Math.random() * possible_letters.length));
						}
						if( _new_nr > 3 ){
							_new_nr = _new_nr - 3;
							for( var i=0; i < _new_nr; i++ ){
								text2 += possible_all.charAt(Math.floor(Math.random() * possible_all.length));
							}
							for( var i=0; i < 3; i++ ){
								text3 += possible_nr.charAt(Math.floor(Math.random() * possible_nr.length));
							}
						}
						else{
							for( var i=0; i < _new_nr; i++ ){
								text2 += possible_all.charAt(Math.floor(Math.random() * possible_all.length));
							}
						}
						
						text = text1 +'_'+ text2 + text3;
					}
					else{
						for( var i=0; i < _nr; i++ ){
							text += possible_all.charAt(Math.floor(Math.random() * possible_all.length));
						}
					}
					

					return text;
				},

				livetext: function( field, section ){
					$( '.themes-repeatable-block' ).on( 'keyup change', field, function(){
						var thisval = $(this).val();
						$(this).parents('.acc_section').find( section ).text( thisval );
					});
				},

				repeatableThemes: function( _mode ){

					$('.demonstrator_themes').smk_Accordion({
						closeAble: true
					});

					// Live update title and category(and other) on typing
					self.livetext( '.this-section-theme-title-field', '.section-theme-title' );
					self.livetext( '.this-section-theme-id-field', '.section-theme-id' );
					self.livetext( '.this-section-theme-category-field', '.section-theme-category' );

					// Delete section
					$('.themes-repeatable-block').on( 'click', '.delete-theme', function( event ){
						event.preventDefault();
						var lists = $(this).parents('.themes-repeatable-block').children();
						if( lists.length > 1 ){
							$(this).parents('.acc_section').slideUp(150, false, function(){
								$(this).remove();
							});
						}
					});

					// Add new section
					$( '.demonstrator_add_theme' ).on( 'click', function(){
						var array_key = self.uniqid( 5, false),
						    the_ul    = $(this).prev('.themes-repeatable-block'),
						    cloned    = $(the_ul).find('.sfa-theme-noindex').clone();

						$( the_ul ).children().removeClass('acc_active');
						$( the_ul ).find('.acc_content').slideUp(150);
						$( the_ul ).append( cloned.hide().addClass('acc_active') );
						cloned.children('.acc_content').show();
						cloned.slideDown(150);
						cloned.removeClass('sfa-theme-noindex');

						cloned.find('.this-section-theme-id-field').val( array_key ).trigger( 'change' );

						cloned.find('input, select').each(function(){
							$(this).attr( 
								'name', 
								$(this).attr( 'name' ).replace( '__noindex__', array_key ) 
							);
						});
					});

				},

				repeatableStyles: function(){

					// Add new section
					$( '.demonstrator_themes' ).on( 'click', '.demonstrator_add_style', function(){
						var array_key = self.uniqid( 5, false),
						    the_ul    = $(this).parent().prev('.demonstrator_styles'),
						    cloned    = $(the_ul).find('.sfa-style-noindex').clone();

						$( the_ul ).append( cloned.hide() );
						cloned.slideDown(150);
						cloned.removeClass('sfa-style-noindex');

						cloned.find('.this-section-style-id-field').val( array_key ).trigger( 'change' );

						cloned.find('input, select').each(function(){
							$(this).attr( 
								'name', 
								$(this).attr( 'name' ).replace( '__stylenoindex__', array_key ) 
							);
						});
					});

					// Delete style
					$('.demonstrator_styles').on( 'click', '.delete-style', function( event ){
						event.preventDefault();
						var lists = $(this).parents('.demonstrator_styles').children();
						if( lists.length > 1 ){
							$(this).parent().slideUp(150, false, function(){
								$(this).remove();
							});
						}
					});

				},

				/*
				-------------------------------------------------------------------------------
				Construct plugin
				-------------------------------------------------------------------------------
				*/
				__construct: function(){
					self = this;

					self.openFrame();
					self.repeatableThemes();
					self.repeatableStyles();

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

	$( document ).on( 'ready load', function(){
		$( document ).DEMONSTRATOR_Plugin();
	} );

})(jQuery);