/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { RichText } from '@wordpress/block-editor';

export default ({ className, attributes: { content }, setAttributes }) => (
    <div className={className}>
        <RichText
            className="wp-block-posty-testimonial__content"
            value={content}
            keepPlaceholderOnFocus
            allowedFormats={[]}
            withoutInteractiveFormatting
            placeholder={__('Enter text', 'posty-starter-theme')}
            onChange={(content) => setAttributes({ content })}
        />
    </div>
);
