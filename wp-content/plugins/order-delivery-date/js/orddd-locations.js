/**
 * JS to add Locations table in the admin 
 * 
 * @namespace orddd_locations_js
 * @since 6.0
 */

var controlled = false;
var shifted    = false;
var hasFocus   = false;
( function( $, data, wp, ajaxurl ) {
	$( function() {

		if ( ! String.prototype.trim ) {
			String.prototype.trim = function () {
				return this.replace( /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '' );
			};
		}

		var rowTemplate        = wp.template( 'orddd-locations-rows' ),
			rowTemplateEmpty   = wp.template( 'orddd-locations-rows-empty' ),
			$table             = $( '.orddd-locations-table' ),
			$tbody             = $( '#locations_settings' ),
			$save_button       = $( 'input[name="save"]' ),
			$submit            = $( '.submit .button-primary[name=save_locations]' ),
			OrdddLocationsModelConstructor = Backbone.Model.extend({
				changes: {},
				/**
		         * Set Locations attributes
		         *
		         * @function setSettingAttribute
		         * @memberof OrdddLocationsModelConstructor
		         * @since 6.0
		         */
				setSettingAttribute: function( rowID, attribute, value ) {
					var locations  = _.indexBy( this.get( 'orddd_locations' ), 'row_id' ),
						changes = {};

					if ( locations[ rowID ][ attribute ] !== value ) {
						changes[ rowID ] = {};
						changes[ rowID ][ attribute ] = value;
						locations[ rowID ][ attribute ]   = value;
					}

					this.logChanges( changes );
				},

				/**
		         * Logs the changes done in the locations
		         *
		         * @param {array} changedRows - Array of changed rows
		         * 
		         * @function logChanges
		         * @memberof OrdddLocationsModelConstructor
		         * @since 6.0
		         */
				logChanges: function( changedRows ) {
					var changes = this.changes || {};
					_.each( changedRows, function( row, id ) {
						changes[ id ] = _.extend( changes[ id ] || {
							row_id : id
						}, row );
					} );

					this.changes = changes;
					this.trigger( 'change:orddd_locations' );
				},

				/**
		         * Block the locations table
		         *
		         * @function block
		         * @memberof OrdddLocationsModelConstructor
		         * @since 6.0
		         */
				block: function() {
					$( '.orddd-locations-table' ).block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});
				},

				/**
		         * Unblock the locations table
		         *
		         * @function block
		         * @memberof OrdddLocationsModelConstructor
		         * @since 6.0
		         */
				unblock: function() {
					$( '.orddd-locations-table' ).unblock();
				},

				/**
		         * Save the locations added
		         *
		         * @function save
		         * @memberof OrdddLocationsModelConstructor
		         * @since 6.0
		         */
				save: function() {
					var self = this;

					self.block();

					Backbone.ajax({
						method: 'POST',
						dataType: 'json',
						url: ajaxurl + ( ajaxurl.indexOf( '?' ) > 0 ? '&' : '?' ) + 'action=orddd_locations_save_changes',
						data: {
							current_class: data.current_class,
							changes: self.changes
						},

						/**
				         * Called on success of ajax calls
				         *
				         * @param {object} response - Response sent from the ajax
				         * @param {string} textStatus - Status of the ajax call
				         *
				         * @function success
				         * @memberof OrdddLocationsModelConstructor
				         * @since 6.0
				         */
						success: function( response, textStatus ) {
							if ( 'success' === textStatus ) {
								OrdddLocationsModelInstance.set( 'orddd_locations', response.data.orddd_locations );
								OrdddLocationsModelInstance.trigger( 'change:orddd_locations' );

								OrdddLocationsModelInstance.changes = {};
								OrdddLocationsModelInstance.trigger( 'saved:orddd_locations' );

								// Reload view.
								OrdddLocationsInstance.render();

							}

							self.unblock();
						}
					});
				}
			} ),
			OrdddLocationsViewConstructor = Backbone.View.extend({
				rowTemplate: rowTemplate,

				/**
		         * Initialize the view events
		         *
		         * @function initialize
		         * @memberof OrdddLocationsViewConstructor
		         * @since 6.0
		         */
				initialize: function() {
					this.listenTo( this.model, 'change:orddd_locations', this.setUnloadConfirmation );
					this.listenTo( this.model, 'saved:orddd_locations', this.clearUnloadConfirmation );

					$tbody.on( 'change autocompletechange', ':input', { view: this }, this.updateModelOnChange );
					$table.on( 'focus click', ':input', { view: this }, this.focusInput );
					$( window ).on( 'beforeunload', { view: this }, this.unloadConfirmation );
					$submit.on( 'click', { view: this }, this.onSubmit );
					$save_button.attr( 'disabled','disabled' );

					//Select multiple rows
					$( document.body ).bind( 'keyup keydown', function( e ) {
						shifted    = e.shiftKey;
						controlled = e.ctrlKey || e.metaKey;
					});

					// Can bind these directly to the buttons, as they won't get overwritten.
					$table.find( '.orddd_locations_insert' ).on( 'click', { view: this }, this.onAddNewRow );
					$table.find( '.orddd_locations_remove' ).on( 'click', { view: this }, this.onDeleteRow );
				}, 

				/**
		         * Add new row in the locations table
		         *
		         * @function render
		         * @memberof OrdddLocationsViewConstructor
		         * @since 6.0
		         */
				render: function() {
					var settings = _.toArray( this.model.get( 'orddd_locations' ) ),
						view     = this;
					this.$el.empty();
					// Blank out the contents.
					if ( settings.length ) {
						// Populate $tbody with the current page of results.
						$.each( settings, function( id, rowData ) {
							view.$el.append( view.rowTemplate( rowData ) );
						} );
					} else {
						view.$el.append( rowTemplateEmpty() );
					}
				},
				updateUrl: function() {
					if ( ! window.history.replaceState ) {
						return;
					}

					var url    = data.base_url;

					if ( 1 < this.page ) {
						url += '&p=' + encodeURIComponent( this.page );
					}

					window.history.replaceState( {}, '', url );
				},

				/**
		         * Change the color of the row which is selected
		         *
		         * @param {object} event - Event triggered
		         * 
		         * @function focusInput
		         * @memberof OrdddLocationsViewConstructor
		         * @since 6.0
		         */
				focusInput: function( event ) {
					var $this_table = $( this ).closest( 'table, tbody' );
					var $this_row   = $( this ).closest( 'tr' );
					if ( ( event.type === 'focus' && hasFocus !== $this_row.index() ) || ( event.type === 'click' && $( this ).is( ':focus' ) ) ) {
						hasFocus = $this_row.index();
						if ( ! shifted && ! controlled ) {
							$( 'tr', $this_table ).removeClass( 'current' ).removeClass( 'last_selected' );
							$( 'tr', $this_table ).children().removeAttr( 'style' );
							$( 'tr', $this_table ).children().children().removeAttr( 'style' );
							$this_row.addClass( 'current' ).addClass( 'last_selected' );
							$this_row.children().attr( 'style', 'background-color:#FFFACD' );
							$this_row.children().children().attr( 'style', 'background-color:#FFFACD' );

						} else if ( shifted ) {
							$( 'tr', $this_table ).removeClass( 'current' );
							$( 'tr', $this_table ).children().removeAttr( 'style' );
							$( 'tr', $this_table ).children().children().removeAttr( 'style' );
							$this_row.addClass( 'selected_now' ).addClass( 'current' );
							if ( $( 'tr.last_selected', $this_table ).length > 0 ) {
								if ( $this_row.index() > $( 'tr.last_selected', $this_table ).index() ) {
									$( 'tr', $this_table ).slice( $( 'tr.last_selected', $this_table ).index(), $this_row.index() ).addClass( 'current' );
								} else {
									$( 'tr', $this_table ).slice( $this_row.index(), $( 'tr.last_selected', $this_table ).index() + 1 ).addClass( 'current' );
								}
							}

							$( 'tr', $this_table ).removeClass( 'last_selected' );
							$this_row.addClass( 'last_selected' );
							$this_row.children().attr( 'style', 'background-color:#FFFACD' );
							$this_row.children().children().attr( 'style', 'background-color:#FFFACD' );
						} else {
							$( 'tr', $this_table ).removeClass( 'last_selected' );
							if ( controlled && $( this ).closest( 'tr' ).is( '.current' ) ) {
								$this_row.removeClass( 'current' );
								$this_row.children().removeAttr( 'style' );
								$this_row.children().children().removeAttr( 'style' );
							} else {
								$this_row.addClass( 'current' ).addClass( 'last_selected' );
								$this_row.children().attr( 'style', 'background-color:#FFFACD' );
								$this_row.children().children().attr( 'style', 'background-color:#FFFACD' );
							}
						}
						$( 'tr', $this_table ).removeClass( 'selected_now' );
					}
				},

				/**
		         * Called when save settings button is clicked
		         *
		         * @param {object} event - Event triggered
		         * 
		         * @function onSubmit
		         * @memberof  OrdddLocationsViewConstructor
		         * @since 6.0
		         */
				onSubmit: function( event ) {
					event.data.view.model.save();
					event.preventDefault();
				},

				/**
		         * Add a new row when clicked on Insert row button
		         *
		         * @param {object} event - Event triggered
		         * 
		         * @function onAddNewRow
		         * @memberof OrdddLocationsViewConstructor
		         * @since 6.0
		         */
				onAddNewRow: function( event ) {
					var view    = event.data.view,
						model   = view.model,
						orddd_locations   = _.indexBy( model.get( 'orddd_locations' ), 'row_id' ),
						changes = {};

					var orddd_locations_keys = Object.keys( orddd_locations );
					var size = 0;
					for( i=0; i < orddd_locations_keys.length; i++ ) {
						var key = orddd_locations_keys[ i ].split( '_' )[ 2 ];
						if( typeof( key ) == 'undefined' ) {
							var key = orddd_locations_keys[ i ].split( '-' )[ 1 ];
						}
						
						if( typeof( orddd_locations_keys[ i + 1 ] ) !== 'undefined' ) {
							var key_2 = orddd_locations_keys[ i + 1 ].split( '_' )[ 2 ];
							if( typeof( key_2 ) == 'undefined' ) {
								var key_2 = orddd_locations_keys[ i + 1 ].split( '-' )[ 1 ];
							}
							if( key_2 > key ) {
								size = key_2;
							} else {
								size = key;
							}
						} else {
							size = key;
						}
					}
					size = parseInt( size ) + 1;
					var	newRow  = _.extend( {}, data.default_settings, {
							row_id: 'new-' + size,
							newRow: true
						} ),
						$current, current_id, current_order;

					$current = $tbody.children( '.current' );
					
					orddd_locations[ newRow.row_id ]   = newRow;
					changes[ newRow.row_id ] = newRow;
					
					model.set( 'orddd_locations', orddd_locations );
					model.logChanges( changes );

					view.render();
				},
				/**
		         * Delete selected row when clicked on Remove select row button
		         *
		         * @param {object} event - Event triggered
		         * 
		         * @function onDeleteRow
		         * @memberof OrdddLocationsViewConstructor
		         *
		         * @since 6.0
		         */
				onDeleteRow: function( event ) {
					var view    = event.data.view,
						model   = view.model,
						orddd_locations = _.indexBy( model.get( 'orddd_locations' ), 'row_id' ),
						changes = {},
						$current, current_id;

					event.preventDefault();

					if ( $current = $tbody.children( '.current' ) ) {
						$current.each(function(){
							current_id    = $( this ).data('id');
							delete orddd_locations[ current_id ];
							changes[ current_id ] = _.extend( changes[ current_id ] || {}, { deleted : 'deleted' } );
						});
						
						model.set( 'orddd_locations', orddd_locations );
						model.logChanges( changes );

						view.render();
					} else {
						window.alert( data.strings.no_rows_selected );
					}
				},

				/**
		         * Called when any changes are done to the locations
		         *
		         * @function setUnloadConfirmation
		         * @memberof OrdddLocationsViewConstructor
		         * @since 6.0
		         */
				setUnloadConfirmation: function() {
					this.needsUnloadConfirm = true;
					$save_button.removeAttr( 'disabled' );
				},

				/**
		         * Called when the changes are saved
		         *
		         * @function clearUnloadConfirmation
		         * @memberof OrdddLocationsViewConstructor
		         * @since 6.0
		         */
				clearUnloadConfirmation: function() {
					this.needsUnloadConfirm = false;
					$save_button.attr( 'disabled', 'disabled' );
					$( "<div id=\"successMessage\" class=\"updated\"><p>" + data.strings.success_message + "</p></div>" ).insertBefore( $( ".orddd_locations_heading" ) );
					setTimeout( function(){ 						
						$( '#successMessage' ).fadeOut( 'fast' );
					}, 2000 );
				},


				/**
		         * Called before unloading the event
		         *
		         * @param {object} event - Event triggered
		         * 
		         * @returns {string} Confirmation message
		         *
		         * @function unloadConfirmation
		         * @memberof OrdddLocationsViewConstructor
		         *
		         * @since 6.0
		         */
				unloadConfirmation: function( event ) {
					if ( event.data.view.needsUnloadConfirm ) {
						event.returnValue = data.strings.unload_confirmation_msg;
						window.event.returnValue = data.strings.unload_confirmation_msg;
						return data.strings.unload_confirmation_msg;
					}
				},

				/**
		         * Called when changes are auto saved
		         *
		         * @param {object} event - Event triggered
		         *
		         * @function updateModelOnChange
		         * @memberof OrdddLocationsViewConstructor
		         *
		         * @since 6.0
		         */
				updateModelOnChange: function( event ) {
					var model     = event.data.view.model,
						$target   = $( event.target ),
						id        = $target.closest( 'tr' ).data( 'id' ),
						attribute = $target.data( 'attribute' ),
						val       = $target.val();
					model.setSettingAttribute( id, attribute, val );
				},
			} ),
			OrdddLocationsModelInstance = new OrdddLocationsModelConstructor({
				orddd_locations: data.orddd_locations
			} ),
			OrdddLocationsInstance = new OrdddLocationsViewConstructor({
				model:    OrdddLocationsModelInstance,
				el:       '#locations_settings'
			} );

		OrdddLocationsInstance.render();

	});
})( jQuery, locations, wp, ajaxurl );
