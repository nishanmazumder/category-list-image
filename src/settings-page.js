import { useState, useEffect } from '@wordpress/element';
import { TextControl, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

const CategoryListImageSettings = () => {
	const [categoryListImage, setCategoryListImage] = useState('');

	// Fetch the current saved setting using WordPress options API (AJAX or directly from options)
	useEffect(() => {
		// Fetch the current setting from the server
		apiFetch({ path: '/wp/v2/settings' })
			.then(response => {
				// Update the state with the saved setting value
				setCategoryListImage(response.category_list_image || '');
			})
			.catch(error => console.error('Error fetching settings:', error));
	}, []);

	// Handle the change in the input field
	const handleChange = (value) => {
		setCategoryListImage(value);
	};

	// Save settings via AJAX to the server
	const handleSave = () => {
		apiFetch({
			path: '/wp/v2/settings', // Endpoint for updating settings
			method: 'POST',
			data: { category_list_image: categoryListImage },
		})
			.then(response => console.log('Settings saved:', response))
			.catch(error => console.error('Error saving settings:', error));
	};

	return (
		<div>
			<h2>{ __('Category List Image Settings', 'plugin-text-domain') }</h2>
			<TextControl
				label={ __('Category List Image URL', 'plugin-text-domain') }
				value={ categoryListImage }
				onChange={ handleChange }
			/>
			<Button isPrimary onClick={ handleSave }>
				{ __('Save Settings', 'plugin-text-domain') }
			</Button>
		</div>
	);
};

export default CategoryListImageSettings;
