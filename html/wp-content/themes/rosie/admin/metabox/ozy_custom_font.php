<?php

return array(
	'id'          => 'ozy_rosie_meta_font',
	'types'       => array('ozy_fonts'),
	'title'       => __('Font Options', 'vp_textdomain'),
	'priority'    => 'high',
	'template'    => array(

		array(
			'type'      => 'group',
			'repeating' => true,
			'length'    => 1,
			'name'      => 'ozy_rosie_meta_font_group',
			'title'     => __('Custom Font', 'vp_textdomain'),
			'fields'    => array(	
				array(
					'type' => 'textbox',
					'name' => 'ozy_rosie_meta_font_id',
					'label' => __('Font Identifier', 'vp_textdomain'),
					'description' => __('Exact name of the font. * Font type name', 'vp_textdomain'),
					'default' => '',
					'validation' => 'required'
				),	
				array(
					'type' => 'upload',
					'name' => 'ozy_rosie_meta_font_eot',
					'label' => __('EOT File', 'vp_textdomain'),
					'description' => __('Upload or choose a EOT font file.', 'vp_textdomain'),
					'validation' => 'required'
				),
				array(
					'type' => 'upload',
					'name' => 'ozy_rosie_meta_font_woff',
					'label' => __('WOFF File', 'vp_textdomain'),
					'description' => __('Upload or choose a WOFF font file.', 'vp_textdomain'),
					'validation' => 'required'
				),
				array(
					'type' => 'upload',
					'name' => 'ozy_rosie_meta_font_ttf',
					'label' => __('TTF File', 'vp_textdomain'),
					'description' => __('Upload or choose an TTF font file.', 'vp_textdomain'),
					'validation' => 'required'
				),
				array(
					'type' => 'upload',
					'name' => 'ozy_rosie_meta_font_svg',
					'label' => __('SVG File', 'vp_textdomain'),
					'description' => __('Upload or choose an SVG font file.', 'vp_textdomain'),
					'validation' => 'required'
				),		
				array(
					'type' => 'radiobutton',
					'name' => 'ozy_rosie_meta_font_weight',
					'label' => __('Font Weight', 'vp_textdomain'),
					'items' => array(
						array(
							'value' => 'normal',
							'label' => 'Normal',
						),
						array(
							'value' => '100',
							'label' => '100',
						),
						array(
							'value' => '200',
							'label' => '200',
						),
						array(
							'value' => '300',
							'label' => '300',
						),
						array(
							'value' => '400',
							'label' => '400',
						),
						array(
							'value' => '500',
							'label' => '500',
						),
						array(
							'value' => '600',
							'label' => '600',
						),
						array(
							'value' => '700',
							'label' => '700',
						),
						array(
							'value' => '800',
							'label' => '800',
						),
						array(
							'value' => '900',
							'label' => '900',
						),																																
					),
					'validation' => 'required',
					'default' => array(
						'normal',
					),
				),
				array(
					'type' => 'radiobutton',
					'name' => 'ozy_rosie_meta_font_style',
					'label' => __('Font Style', 'vp_textdomain'),
					'items' => array(
						array(
							'value' => 'normal',
							'label' => 'Normal',
						),
						array(
							'value' => 'italic',
							'label' => 'Italic',
						),
						array(
							'value' => 'oblique',
							'label' => 'Oblique',
						)																															
					),
					'validation' => 'required',
					'default' => array(
						'normal',
					),
				)
			)
		)				
	),	
);

/**
 * EOF
 */