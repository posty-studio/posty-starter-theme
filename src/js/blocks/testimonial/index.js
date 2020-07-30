/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import edit from './edit';

export default () => {
    registerBlockType('posty/testimonial', {
        title: __('Testimonial', 'posty-starter-theme'),
        description: __('Shows a testimonial.', 'posty-starter-theme'),
        category: 'common',
        icon: 'quote',
        attributes: {
            content: {
                type: 'string',
            },
        },
        edit,
        save: () => {},
    });
};
