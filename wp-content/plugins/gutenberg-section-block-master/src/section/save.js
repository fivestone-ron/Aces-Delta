/**
 * BLOCK: section-block
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */
import classnames from 'classnames'
import Section from './section-tag'

const {
	InnerBlocks,
	getColorClassName,
} = wp.editor;

export default ( { attributes, className } ) => {

	const {
		tagName,
		backgroundColor,
		customTextColor,
		customBackgroundColor,
		spacingBottom,
		spacingTop,
		bgImage,
		bgOptions,
		enableSpacing,
		sectionType
	} = attributes;
	const backgroundClass = getColorClassName( 'background-color', backgroundColor );
	const classes = classnames(
		{
			className,
			[backgroundClass]: backgroundClass
		}
	)

	const styles = {
		backgroundColor: backgroundClass ? undefined : customBackgroundColor,
		color: customTextColor ? customTextColor : undefined,
		paddingBottom: (!! enableSpacing) && spacingBottom ? spacingBottom : undefined,
		paddingTop: (!! enableSpacing) && spacingTop ? spacingTop : undefined,
	}
	const typeBasicContent = [<div class="basic-content-section"><div class="ctn-main"><InnerBlocks.Content /></div></div>];
	const typeFullWidth = [<div class="full-width-section"><div><InnerBlocks.Content /></div></div>];
	const typeSmall = [<div class="basic-content-section"><div class="ctn-main-small"><InnerBlocks.Content /></div></div>];

	return (
		<Section tagName={tagName} className={ classes ? classes : undefined } style={ styles }>
			{ !! bgImage && <div
				className={ classnames(
					'section-bg', {
						'bg__repeated': bgOptions.repeat,
						'bg__stretched': bgOptions.stretch || bgOptions.fixed,
						'bg__fixed': bgOptions.fixed,
					} ) }
				style={ {
					backgroundImage: bgImage ? 'url(' + bgImage.image.url + ')' : undefined,
					opacity: bgOptions.opacity
				} }
			/> }
			{ sectionType === 'fullWidth' ? typeFullWidth : sectionType === 'smaller' ? typeSmall : typeBasicContent }
		</Section>
	);
}