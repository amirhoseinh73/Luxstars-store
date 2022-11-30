/**
 * JS to add Weekdays settings table in the admin 
 * 
 * @namespace orddd_weekday_settings_js
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

		var rowTemplate        = wp.template( 'orddd-advance-setting-rows' ),
			rowTemplateEmpty   = wp.template( 'orddd-advance-setting-rows-empty' ),
			$table             = $( '.orddd-advance-settings-table' ),
			$tbody             = $( '#settings' ),
			$save_button       = $( 'input[name="save"]' ),
			$submit            = $( '.submit .button-primary[type=submit]' ),
			ORDDDAdvanceSettingsModelConstructor = Backbone.Model.extend({
				changes: {},
				/**
		         * Set weekday settings attributes
		         *
		         * @function setSettingAttribute
		         * @memberof ORDDDAdvanceSettingsModelConstructor
		         * @since 6.0
		         */
				setSettingAttribute: function( rowID, attribute, value ) {
					var advance_settings   = _.indexBy( this.get( 'orddd_advance_settings' ), 'row_id' ),
						changes = {};

					if ( advance_settings[ rowID ][ attribute ] !== value ) {
						changes[ rowID ] = {};
						changes[ rowID ][ attribute ] = value;
						advance_settings[ rowID ][ attribute ]   = value;
					}

					this.logChanges( changes );
				},

				/**
		         * Logs the changes done in the weekday settings
		         *
		         * @param {array} changedRows - Array of changed rows
		         * 
		         * @function logChanges
		         * @memberof ORDDDAdvanceSettingsModelConstructor
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
					this.trigger( 'change:orddd_advance_settings' );
				},

				/**
		         * Block the weekdays settings table
		         *
		         * @function block
		         * @memberof ORDDDAdvanceSettingsModelConstructor
		         * @since 6.0
		         */
				block: function() {
					$( '.orddd-advance-settings-table' ).block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});
				},

				/**
		         * Unblock the weekdays settings table
		         *
		         * @function block
		         * @memberof ORDDDAdvanceSettingsModelConstructor
		         * @since 6.0
		         */
				unblock: function() {
					$( '.orddd-advance-settings-table' ).unblock();
				},

				/**
		         * Save the weekdays settings added
		         *
		         * @function save
		         * @memberof ORDDDAdvanceSettingsModelConstructor
		         * @since 6.0
		         */
				save: function() {
					var self = this;

					self.block();

					Backbone.ajax({
						method: 'POST',
						dataType: 'json',
						url: ajaxurl + ( ajaxurl.indexOf( '?' ) > 0 ? '&' : '?' ) + 'action=orddd_advance_settings_save_changes',
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
				         * @memberof ORDDDAdvanceSettingsModelConstructor
				         * @since 6.0
				         */
						success: function( response, textStatus ) {
							if ( 'success' === textStatus ) {
								ORDDDAdvanceSettingsModelInstance.set( 'orddd_advance_settings', response.data.orddd_advance_settings );
								ORDDDAdvanceSettingsModelInstance.trigger( 'change:orddd_advance_settings' );

								ORDDDAdvanceSettingsModelInstance.changes = {};
								ORDDDAdvanceSettingsModelInstance.trigger( 'saved:orddd_advance_settings' );

								// Reload view.
								ORDDDAdvanceSettingsInstance.render();

							}

							self.unblock();
						}
					});
				}
			} ),
			ORDDDAdvanceSettingsViewConstructor = Backbone.View.extend({
				rowTemplate: rowTemplate,

				/**
		         * Initialize the view events
		         *
		         * @function initialize
		         * @memberof ORDDDAdvanceSettingsViewConstructor
		         * @since 6.0
		         */
				initialize: function() {
					this.listenTo( this.model, 'change:orddd_advance_settings', this.setUnloadConfirmation );
					this.listenTo( this.model, 'saved:orddd_advance_settings', this.clearUnloadConfirmation );

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
					$table.find( '.orddd_advance_settings_insert' ).on( 'click', { view: this }, this.onAddNewRow );
					$table.find( '.orddd_advance_settings_remove' ).on( 'click', { view: this }, this.onDeleteRow );
				}, 

				/**
		         * Add new row in the weekday settings table
		         *
		         * @function render
		         * @memberof ORDDDAdvanceSettingsViewConstructor
		         * @since 6.0
		         */
				render: function() {
					var settings = _.toArray( this.model.get( 'orddd_advance_settings' ) ),
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
		         * @memberof ORDDDAdvanceSettingsViewConstructor
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
		         * @memberof ORDDDAdvanceSettingsViewConstructor
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
		         * @memberof ORDDDAdvanceSettingsViewConstructor
		         * @since 6.0
		         */
				onAddNewRow: function( event ) {
					var view    = event.data.view,
						model   = view.model,
						orddd_advance_settings   = _.indexBy( model.get( 'orddd_advance_settings' ), 'row_id' ),
						changes = {},
						size    = _.size( orddd_advance_settings ) + 1,
						newRow  = _.extend( {}, data.default_settings, {
							row_id: 'new-' + size,
							newRow: true
						} ),
						$current, current_id, current_order;

					$current = $tbody.children( '.current' );
					
					orddd_advance_settings[ newRow.row_id ]   = newRow;
					changes[ newRow.row_id ] = newRow;
					
					model.set( 'orddd_advance_settings', orddd_advance_settings );
					model.logChanges( changes );

					view.render();
				},
				/**
		         * Delete selected row when clicked on Remove select row button
		         *
		         * @param {object} event - Event triggered
		         * 
		         * @function onDeleteRow
		         * @memberof ORDDDAdvanceSettingsViewConstructor
		         *
		         * @since 6.0
		         */
				onDeleteRow: function( event ) {
					var view    = event.data.view,
						model   = view.model,
						orddd_advance_settings   = _.indexBy( model.get( 'orddd_advance_settings' ), 'row_id' ),
						changes = {},
						$current, current_id;

					event.preventDefault();

					if ( $current = $tbody.children( '.current' ) ) {
						$current.each(function(){
							current_id    = $( this ).data('id');
							delete orddd_advance_settings[ current_id ];
							changes[ current_id ] = _.extend( changes[ current_id ] || {}, { deleted : 'deleted' } );
						});
						
						model.set( 'orddd_advance_settings', orddd_advance_settings );
						model.logChanges( changes );

						view.render();
					} else {
						window.alert( data.strings.no_rows_selected );
					}
				},

				/**
		         * Called when any changes are done to the weekdays settings
		         *
		         * @function setUnloadConfirmation
		         * @memberof ORDDDAdvanceSettingsViewConstructor
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
		         * @memberof ORDDDAdvanceSettingsViewConstructor
		         * @since 6.0
		         */
				clearUnloadConfirmation: function() {
					this.needsUnloadConfirm = false;
					$save_button.attr( 'disabled', 'disabled' );
				},


				/**
		         * Called before unloading the event
		         *
		         * @param {object} event - Event triggered
		         * 
		         * @returns {string} Confirmation message
		         *
		         * @function unloadConfirmation
		         * @memberof ORDDDAdvanceSettingsViewConstructor
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
		         * @memberof ORDDDAdvanceSettingsViewConstructor
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
			ORDDDAdvanceSettingsModelInstance = new ORDDDAdvanceSettingsModelConstructor({
				orddd_advance_settings: data.orddd_advance_settings
			} ),
			ORDDDAdvanceSettingsInstance = new ORDDDAdvanceSettingsViewConstructor({
				model:    ORDDDAdvanceSettingsModelInstance,
				el:       '#settings'
			} );

		ORDDDAdvanceSettingsInstance.render();

	});
})( jQuery, htmlAdvanceSettingsLocalizeScript, wp, ajaxurl );
