const { __ } = wp.i18n;
const { addFilter } = wp.hooks;
const { Fragment, RawHTML } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { createHigherOrderComponent } = wp.compose;
const { ToggleControl, PanelBody } = wp.components;

const allowedBlocks = [ 'core/rss' ];

function addRSSImageAttribute( settings ) {
	settings.attributes = Object.assign( settings.attributes, {
		rssimage: {
			type: 'boolean',
			default: false,
		},
	} );

	return settings;
}

const withRSSImageControl = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		const { name, attributes, setAttributes, isSelected } = props;

		const { rssimage } = attributes;

		return (
			<Fragment>
				<BlockEdit { ...props } />
				{ isSelected && allowedBlocks.includes( name ) && (
					<InspectorControls>
						<PanelBody>
							<ToggleControl
								label={ __( 'Show Featured Image?' ) }
								checked={ !! rssimage }
								onChange={ () =>
									setAttributes( {
										rssimage: ! rssimage,
									} )
								}
								help={
									!! rssimage
										? __(
												'Featured Image is visible.',
												'ucsc'
										  )
										: __(
												'Featured Image is hidden.',
												'ucsc'
										  )
								}
							/>
						</PanelBody>
					</InspectorControls>
				) }
			</Fragment>
		);
	};
}, 'withRSSImageControl' );

function withRSSImageProps( element, blockType, attributes ) {
	const { rssimage } = attributes;

	console.log( element );
	if ( ! allowedBlocks.includes( blockType.name ) || ! rssimage ) {
		return element;
	}

	console.log( attributes );

	return '';
}

addFilter(
	'blocks.registerBlockType',
	'tribe/add-rssimage-attribute',
	addRSSImageAttribute
);

addFilter(
	'editor.BlockEdit',
	'tribe/add-rssimage-control',
	withRSSImageControl
);

addFilter(
	'blocks.getSaveElement',
	'tribe/inject-rssimage-attribute',
	withRSSImageProps
);
