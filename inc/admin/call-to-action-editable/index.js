( function( wp ) {
	/**
	 * Registers a new block provided a unique name and an object defining its behavior.
	 * @see https://github.com/WordPress/gutenberg/tree/master/blocks#api
	 */
	var registerBlockType = wp.blocks.registerBlockType;
	/**
	 * Returns a new element of given type. Element is an abstraction layer atop React.
	 * @see https://github.com/WordPress/gutenberg/tree/master/element#element
	 */
	var el = wp.element.createElement;
	/**
	 * Retrieves the translation of text.
	 * @see https://github.com/WordPress/gutenberg/tree/master/i18n#api
	 */
	var __ = wp.i18n.__;

	const iconEl = el('svg', { width: 20, height: 20 },
  el('path', { d: "M12.5,12H12v-0.5c0-0.3-0.2-0.5-0.5-0.5H11V6h1l1-2c-1,0.1-2,0.1-3,0C9.2,3.4,8.6,2.8,8,2V1.5C8,1.2,7.8,1,7.5,1 S7,1.2,7,1.5V2C6.4,2.8,5.8,3.4,5,4C4,4.1,3,4.1,2,4l1,2h1v5c0,0-0.5,0-0.5,0C3.2,11,3,11.2,3,11.5V12H2.5C2.2,12,2,12.2,2,12.5V13 h11v-0.5C13,12.2,12.8,12,12.5,12z M7,11H5V6h2V11z M10,11H8V6h2V11z" } )
);


	var TextControl = wp.components.TextControl;

	function content(attributes){
		return '[wpas id=' + attributes.text + ']';
	}

	/**
	 * Every block starts by registering a new block type definition.
	 * @see https://wordpress.org/gutenberg/handbook/block-api/
	 */
	registerBlockType( 'wpas-advanced-search/call-to-action-editable', {
		/**
		 * This is the display title for your block, which can be translated with `i18n` functions.
		 * The block inserter will show this name.
		 */
		title: __( 'Advance Search' ),

		/**
		 * Blocks are grouped into categories to help users browse and discover them.
		 * The categories provided by core are `common`, `embed`, `formatting`, `layout` and `widgets`.
		 */

		icon: React.createElement(
            'svg',
            { xmlns: 'http://www.w3.org/2000/svg', width: '512', height: '512', viewBox: '0 0 512 512' },
            React.createElement('path', { d: 'M460.355 421.59l-106.51-106.512c20.04-27.553 31.884-61.437 31.884-98.037C385.73 124.935 310.792 50 218.685 50c-92.106 0-167.04 74.934-167.04 167.04 0 92.107 74.935 167.042 167.04 167.042 34.912 0 67.352-10.773 94.184-29.158L419.945 462l40.41-40.41zM100.63 217.04c0-65.095 52.96-118.055 118.056-118.055 65.098 0 118.057 52.96 118.057 118.056 0 65.097-52.96 118.057-118.057 118.057-65.096 0-118.055-52.96-118.055-118.056z' })
        ),
		category: 'widgets',

		/**
		 * Optional block extended support features.
		 */
		supports: {
			// Removes support for an HTML mode.
			html: false,
		},

		attributes: {
			text: {
				type: 'string',
				source: 'number',
				selector: 'p',
				default: '1'
			}
		},

		/**
		 * The edit function describes the structure of your block in the context of the editor.
		 * This represents what the editor will render when the block is used.
		 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#edit
		 *
		 * @param {Object} [props] Properties passed from the editor.
		 * @return {Element}       Element to render.
		 */
		edit: function( props ) {
			var attributes = props.attributes;
			if( props.isSelected ){
				/**
				 * Callback when text changes to update attribute
				 * @param newValue
				 */
				function onChangeText(newValue){
					props.setAttributes({
						text: newValue
					});
				}
				/**
				 * Callback when linkText changes to update attribute
				 * @param newValue
				 */
				function onChangeLinkText(newValue){
					props.setAttributes({
						linkText: newValue
					});
				}
				/**
				 * Callback when link changes to update attribute
				 * @param newValue
				 */
				function onChangeLink(newValue){
					props.setAttributes({
						link: newValue
					});
				}
				return el(
					'div',
					{},
					[
						el(
							TextControl,
							{
								label: __( 'Advanced Search Shortcode ID' ),
								value: attributes.text,
								help: 'Please enter advanced search shortcode ID i.e 1 or 2 etc...',
								onChange: onChangeText
							},
						)
					]
				);
			}
			return content(attributes);
		},

		/**
		 * The save function defines the way in which the different attributes should be combined
		 * into the final markup, which is then serialized by Gutenberg into `post_content`.
		 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#save
		 *
		 * @return {Element}       Element to render.
		 */
		save: function(props) {
			return content(props.attributes);
		}
	} );
} )(
	window.wp
);
