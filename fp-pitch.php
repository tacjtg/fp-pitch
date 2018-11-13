<?php
/*
Plugin Name: Finn Partners Pitch Plugin
Plugin URI: 
Description: Create & Display Pitches and Pitch Reports
Version: 1.0
Author: JTG
Author URI: https://jonathangatlin.com
License: GPLv2
*/

class FP_Pitch_Plugin {
	
	public function __construct() {
		
		// Include ACF
        add_filter( 'acf/settings/path', array( $this, 'update_acf_settings_path' ) );
        add_filter( 'acf/settings/dir', array( $this, 'update_acf_settings_dir' ) );
        add_filter('acf/settings/show_admin', '__return_false');
        
		if( ! class_exists('acf') ) {     
        	include_once( plugin_dir_path( __FILE__ ) . 'vendor/acf-pro/acf.php' );
        }
        
        if( ! function_exists('include_field_types_image_crop') ) {  
        	include_once( plugin_dir_path( __FILE__ ) . 'vendor/acf-image-crop/acf-image-crop.php' );
        }
        
        // Register CPTs
		add_action( 'init', array( $this, 'register_fp_media_contacts' ) );
		add_action( 'init', array( $this, 'register_fp_pitches' ) );
		add_action( 'init', array( $this, 'register_fp_pitch_reports' ) );
		
		// Include ACF Options
		add_action( 'init', array( $this, 'fp_media_contact_options' ) );
		add_action( 'init', array( $this, 'fp_pitch_options' ) );
		add_action( 'init', array( $this, 'fp_pitch_report_options' ) );
		
		// Assign Archive & Single Page Templates
		add_filter( 'archive_template', array( $this, 'fp_media_contact_archive_template' ) );	
		add_filter( 'archive_template', array( $this, 'fp_pitch_archive_template' ) );
		add_filter( 'archive_template', array( $this, 'fp_pitch_report_archive_template' ) );
		add_filter( 'single_template', array( $this, 'fp_media_contact_single_template' ) );
		add_filter( 'single_template', array( $this, 'fp_pitch_single_template' ) );		
		add_filter( 'single_template', array( $this, 'fp_pitch_report_single_template' ) );
		
		// Include Styles
		add_action( 'wp_enqueue_scripts', array( $this, 'fp_pitch_frontend_styles' ), 99 );
		
		// Custom Image Sizes
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 50, 50, true );
		add_image_size('lightbox_lg', 1000, 9999);
		add_image_size('lightbox_thumb', 500, 340, array( 'center', 'center' ));
		
		// Allow SVG in Media Uploader
		function fp_custom_mtypes( $m ) {
		    $m['svg'] = 'image/svg+xml';
		    $m['svgz'] = 'image/svg+xml';
		    return $m;
		}
		add_filter( 'upload_mimes', 'fp_custom_mtypes' );
		
		// Format Phone #, Input must be: 6151234567
		function fp_format_phone($phone) {
			return preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $phone);
		}
		
		// Display Errors
		//error_reporting(E_ALL); ini_set('display_errors', 1);

    }
	
	// Include ACF

	public function update_acf_settings_path( $path ) {
	   $path = plugin_dir_path( __FILE__ ) . 'vendor/acf-pro/';
	   return $path;
	}
    
    public function update_acf_settings_dir( $dir ) {
        $dir = plugin_dir_url( __FILE__ ) . 'vendor/acf-pro/';
        return $dir;
    }
    
    // Register CPTs
    
    public function register_fp_media_contacts() {
		$labels = array(
			"name" => __( 'Media Contacts', 'fppitchplugin' ),
			"singular_name" => __( 'Media Contact', 'fppitchplugin' ),
			);
		$args = array(
			"label" => __( 'Media Contacts', 'fppitchplugin' ),
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => false,
			"rest_base" => "",
			"has_archive" => true,
			"show_in_menu" => true,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => array( "slug" => "media-contacts", "with_front" => true ),
			"query_var" => true,
			"menu_icon" => "dashicons-admin-users",
			"supports" => array( 'title' ),
		);
		register_post_type( "fp-media-contacts", $args );
	}
	
	public function register_fp_pitches() {
		$labels = array(
			"name" => __( 'Pitches', 'fppitchplugin' ),
			"singular_name" => __( 'Pitch', 'fppitchplugin' ),
			);
		$args = array(
			"label" => __( 'Pitches', 'fppitchplugin' ),
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => false,
			"rest_base" => "",
			"has_archive" => true,
			"show_in_menu" => true,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => array( "slug" => "pitches", "with_front" => true ),
			"query_var" => true,
			"menu_icon" => "dashicons-id",
			"supports" => array( 'title' ),
		);
		register_post_type( "fp-pitches", $args );
	}
	
	public function register_fp_pitch_reports() {
		$labels = array(
			"name" => __( 'Pitch Reports', 'fppitchplugin' ),
			"singular_name" => __( 'Pitch Report', 'fppitchplugin' ),
			);
		$args = array(
			"label" => __( 'Pitch Reports', 'fppitchplugin' ),
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => false,
			"rest_base" => "",
			"has_archive" => true,
			"show_in_menu" => true,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => array( "slug" => "pitch-reports", "with_front" => true ),
			"query_var" => true,
			"menu_icon" => "dashicons-analytics",
			"supports" => array( 'title' ),
		);
		register_post_type( "fp-pitch-reports", $args );
	}
	
	// Include ACF Options
	
	public function fp_media_contact_options() {
		if( function_exists('acf_add_local_field_group') ):
		acf_add_local_field_group(array(
			'key' => 'group_5aa006484b6a4',
			'title' => 'Media Contact Field Group',
			'fields' => array(
				array(
					'key' => 'field_5aa006947e09e',
					'label' => 'First Name',
					'name' => 'first_name',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5aa006ba7e09f',
					'label' => 'Last Name',
					'name' => 'last_name',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5aa006c57e0a0',
					'label' => 'Email',
					'name' => 'email',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5aa006ea7e0a1',
					'label' => 'Office Phone',
					'name' => 'office_phone',
					'type' => 'text',
					'instructions' => 'Example: 6152441818',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5aa007267e0a2',
					'label' => 'Cell Phone',
					'name' => 'cell_phone',
					'type' => 'text',
					'instructions' => 'Example: 6152441818',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5aa007327e0a3',
					'label' => 'Headshot',
					'name' => 'headshot',
					'type' => 'image_crop',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'crop_type' => 'hard',
					'target_size' => 'custom',
					'width' => 250,
					'height' => 300,
					'preview_size' => 'thumbnail',
					'force_crop' => 'yes',
					'save_in_media_library' => 'yes',
					'retina_mode' => 'no',
					'save_format' => 'url',
					'return_format' => 'url',
					'library' => 'all',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'fp-media-contacts',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => array(
				0 => 'permalink',
				1 => 'the_content',
				2 => 'excerpt',
				3 => 'custom_fields',
				4 => 'discussion',
				5 => 'comments',
				6 => 'revisions',
				7 => 'slug',
				8 => 'author',
				9 => 'format',
				10 => 'page_attributes',
				11 => 'featured_image',
				12 => 'categories',
				13 => 'tags',
				14 => 'send-trackbacks',
			),
			'active' => 1,
			'description' => '',
		));
		
		endif;
	}
	
	public function fp_pitch_options() {
		if( function_exists('acf_add_local_field_group') ):
		acf_add_local_field_group(array(
			'key' => 'group_5a0b671c8f464',
			'title' => 'Media Pitch',
			'fields' => array(
				array(
					'key' => 'field_5a0b67302a2ab',
					'label' => 'Intro',
					'name' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'placement' => 'top',
					'endpoint' => 0,
				),
				array(
					'key' => 'field_5a0b673b2a2ac',
					'label' => 'Pitch Title',
					'name' => 'pitch_title',
					'type' => 'textarea',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'maxlength' => '',
					'rows' => 3,
					'new_lines' => '',
				),
				array(
					'key' => 'field_5a0b67452a2ad',
					'label' => 'Pitch Intro',
					'name' => 'pitch_intro',
					'type' => 'wysiwyg',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'tabs' => 'all',
					'toolbar' => 'full',
					'media_upload' => 0,
					'delay' => 0,
				),
				array(
					'key' => 'field_5a0b67532a2ae',
					'label' => 'Client Logo',
					'name' => 'client_logo',
					'type' => 'image_crop',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'crop_type' => 'hard',
					'target_size' => 'thumbnail',
					'width' => '',
					'height' => '',
					'preview_size' => 'medium',
					'force_crop' => 'yes',
					'save_in_media_library' => 'yes',
					'retina_mode' => 'no',
					'save_format' => 'url',
					'return_format' => 'url',
					'library' => 'all',
				),
				array(
					'key' => 'field_5a0cc456ccc35',
					'label' => 'Include Release?',
					'name' => 'include_release',
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'ui' => 1,
					'ui_on_text' => '',
					'ui_off_text' => '',
				),
				array(
					'key' => 'field_5a0cc474ccc36',
					'label' => 'Release Download',
					'name' => 'release_download',
					'type' => 'file',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_5a0cc456ccc35',
								'operator' => '==',
								'value' => '1',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'return_format' => 'url',
					'library' => 'all',
					'min_size' => '',
					'max_size' => '',
					'mime_types' => '',
				),
				array(
					'key' => 'field_5a0b675e2a2af',
					'label' => 'DVLS Contact',
					'name' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'placement' => 'top',
					'endpoint' => 0,
				),
				array(
					'key' => 'field_5aa008888fa75',
					'label' => 'Media Contact',
					'name' => 'media_contact',
					'type' => 'relationship',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'post_type' => array(
						0 => 'fp-media-contacts',
					),
					'taxonomy' => array(
					),
					'filters' => array(
						0 => 'search',
						1 => 'post_type',
						2 => 'taxonomy',
					),
					'elements' => '',
					'min' => '',
					'max' => '',
					'return_format' => 'object',
				),
				array(
					'key' => 'field_5a0b69092a2ba',
					'label' => 'Documents',
					'name' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'placement' => 'top',
					'endpoint' => 0,
				),
				array(
					'key' => 'field_5a0b69202a2bb',
					'label' => 'Documents',
					'name' => 'documents',
					'type' => 'flexible_content',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'layouts' => array(
						'5a0b693979965' => array(
							'key' => '5a0b693979965',
							'name' => 'document_section',
							'label' => 'Document Section',
							'display' => 'block',
							'sub_fields' => array(
								array(
									'key' => 'field_5a0b69412a2bc',
									'label' => 'Title',
									'name' => 'title',
									'type' => 'text',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'default_value' => '',
									'placeholder' => '',
									'prepend' => '',
									'append' => '',
									'maxlength' => '',
								),
								array(
									'key' => 'field_5a0b69482a2bd',
									'label' => 'Document Links',
									'name' => 'document_links',
									'type' => 'repeater',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'collapsed' => 'field_5a0b695a2a2be',
									'min' => 0,
									'max' => 0,
									'layout' => 'block',
									'button_label' => 'Add Document',
									'sub_fields' => array(
										array(
											'key' => 'field_5a0b695a2a2be',
											'label' => 'Document Title',
											'name' => 'document_title',
											'type' => 'textarea',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '50',
												'class' => '',
												'id' => '',
											),
											'default_value' => '',
											'placeholder' => '',
											'maxlength' => '',
											'rows' => 3,
											'new_lines' => '',
										),
										array(
											'key' => 'field_5a0b69622a2bf',
											'label' => 'Document File',
											'name' => 'document_file',
											'type' => 'file',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '50',
												'class' => '',
												'id' => '',
											),
											'return_format' => 'url',
											'library' => 'all',
											'min_size' => '',
											'max_size' => '',
											'mime_types' => '',
										),
										array(
											'key' => 'field_5a0b697b2a2c0',
											'label' => 'Is Release?',
											'name' => 'is_release',
											'type' => 'true_false',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '30',
												'class' => '',
												'id' => '',
											),
											'message' => '',
											'default_value' => 0,
											'ui' => 1,
											'ui_on_text' => '',
											'ui_off_text' => '',
										),
										array(
											'key' => 'field_5a0b699b2a2c1',
											'label' => 'Release Date',
											'name' => 'release_date',
											'type' => 'date_picker',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => array(
												array(
													array(
														'field' => 'field_5a0b697b2a2c0',
														'operator' => '==',
														'value' => '1',
													),
												),
											),
											'wrapper' => array(
												'width' => '50',
												'class' => '',
												'id' => '',
											),
											'display_format' => 'm/d/Y',
											'return_format' => 'm/d/Y',
											'first_day' => 1,
										),
									),
								),
							),
							'min' => '',
							'max' => '',
						),
					),
					'button_label' => 'Add Section',
					'min' => '',
					'max' => '',
				),
				array(
					'key' => 'field_5a0b69df2a2c2',
					'label' => 'Images',
					'name' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'placement' => 'top',
					'endpoint' => 0,
				),
				array(
					'key' => 'field_5a0b69ef2a2c3',
					'label' => 'Images',
					'name' => 'images',
					'type' => 'repeater',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'collapsed' => 'field_5a0b69f42a2c4',
					'min' => 0,
					'max' => 0,
					'layout' => 'table',
					'button_label' => 'Add Image',
					'sub_fields' => array(
						array(
							'key' => 'field_5a0b69f42a2c4',
							'label' => 'Image Title',
							'name' => 'image_title',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						),
						array(
							'key' => 'field_5a0b69fa2a2c5',
							'label' => 'Image',
							'name' => 'image',
							'type' => 'image_crop',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'crop_type' => 'hard',
							'target_size' => 'thumbnail',
							'width' => '',
							'height' => '',
							'preview_size' => 'medium',
							'force_crop' => 'yes',
							'save_in_media_library' => 'yes',
							'retina_mode' => 'no',
							'save_format' => 'object',
							'return_format' => 'object',
							'library' => 'all',
						),
						array(
							'key' => 'field_5a0dfba6c4a1e',
							'label' => 'High Res File',
							'name' => 'high_res_file',
							'type' => 'file',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'return_format' => 'url',
							'library' => 'all',
							'min_size' => '',
							'max_size' => '',
							'mime_types' => '',
						),
					),
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'fp-pitches',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => array(
				0 => 'the_content',
				1 => 'excerpt',
				2 => 'custom_fields',
				3 => 'discussion',
				4 => 'comments',
				5 => 'revisions',
				6 => 'slug',
				7 => 'author',
				8 => 'format',
				9 => 'featured_image',
				10 => 'categories',
				11 => 'tags',
				12 => 'send-trackbacks',
			),
			'active' => 1,
			'description' => '',
		));
		
		endif;
	}
	
	public function fp_pitch_report_options() {
		if( function_exists('acf_add_local_field_group') ):
		acf_add_local_field_group(array(
			'key' => 'group_5b59ca2ce2d56',
			'title' => 'Pitch Reports',
			'fields' => array(
				array(
					'key' => 'field_5b58e21670766',
					'label' => 'Intro',
					'name' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'placement' => 'top',
					'endpoint' => 0,
				),
				array(
					'key' => 'field_5b58e216707e6',
					'label' => 'Pitch Report Title',
					'name' => 'fp_pitch_report_title',
					'type' => 'textarea',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'maxlength' => '',
					'rows' => 3,
					'new_lines' => '',
				),
				array(
					'key' => 'field_5b58e21670861',
					'label' => 'Pitch Report Intro',
					'name' => 'fp_pitch_report_intro',
					'type' => 'wysiwyg',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'tabs' => 'all',
					'toolbar' => 'full',
					'media_upload' => 0,
					'delay' => 0,
				),
				array(
					'key' => 'field_5b58e216708b8',
					'label' => 'Client Logo',
					'name' => 'fp_pitch_report_client_logo',
					'type' => 'image_crop',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'crop_type' => 'hard',
					'target_size' => 'thumbnail',
					'width' => '',
					'height' => '',
					'preview_size' => 'medium',
					'force_crop' => 'yes',
					'save_in_media_library' => 'yes',
					'retina_mode' => 'no',
					'save_format' => 'url',
					'return_format' => 'url',
					'library' => 'all',
				),
				array(
					'key' => 'field_5b58e21670a31',
					'label' => 'DVLS Contact',
					'name' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'placement' => 'top',
					'endpoint' => 0,
				),
				array(
					'key' => 'field_5b58e21670ab0',
					'label' => 'Media Contact',
					'name' => 'media_contact',
					'type' => 'relationship',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'post_type' => array(
						0 => 'bios',
					),
					'taxonomy' => array(
					),
					'filters' => array(
						0 => 'search',
						1 => 'post_type',
						2 => 'taxonomy',
					),
					'elements' => '',
					'min' => '',
					'max' => '',
					'return_format' => 'object',
				),
				array(
					'key' => 'field_5b58e21670b2e',
					'label' => 'Pitch Reports',
					'name' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'placement' => 'top',
					'endpoint' => 0,
				),
				array(
					'key' => 'field_5b58e21670bab',
					'label' => 'Pitch Report',
					'name' => 'fp_pitch_report_section',
					'type' => 'flexible_content',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'layouts' => array(
						'5b59f905707ae' => array(
							'key' => '5b59f905707ae',
							'name' => 'fp_pitch_report_header',
							'label' => 'Header',
							'display' => 'block',
							'sub_fields' => array(
								array(
									'key' => 'field_5b59f928707af',
									'label' => 'Header Text',
									'name' => 'fp_pitch_report_header_text',
									'type' => 'text',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'default_value' => '',
									'placeholder' => '',
									'prepend' => '',
									'append' => '',
									'maxlength' => '',
								),
							),
							'min' => '',
							'max' => '',
						),
						'5b58e8c90292b' => array(
							'key' => '5b58e8c90292b',
							'name' => 'fp_pitch_report_drumbeat_story',
							'label' => 'Focus/Story',
							'display' => 'block',
							'sub_fields' => array(
								array(
									'key' => 'field_5b6891eaaa75a',
									'label' => 'Focus/Story Header',
									'name' => 'fp_pitch_report_drumbeat_story_header_text',
									'type' => 'text',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'default_value' => '',
									'placeholder' => '',
									'prepend' => '',
									'append' => '',
									'maxlength' => '',
								),
								array(
									'key' => 'field_5b58e8c90292c',
									'label' => 'Focus/Story Title',
									'name' => 'fp_pitch_report_drumbeat_story_title',
									'type' => 'text',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'default_value' => '',
									'placeholder' => '',
									'prepend' => '',
									'append' => '',
									'maxlength' => '',
								),
								array(
									'key' => 'field_5b58e8c90292d',
									'label' => 'Media Targets',
									'name' => 'fp_pitch_report_drumbeat_story_media_targets',
									'type' => 'text',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'default_value' => '',
									'placeholder' => '',
									'prepend' => '',
									'append' => '',
									'maxlength' => '',
								),
								array(
									'key' => 'field_5b58ebeb99142',
									'label' => 'Additional Ideas/Notes',
									'name' => 'fp_pitch_report_drumbeat_story_ideas_notes',
									'type' => 'textarea',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'default_value' => '',
									'placeholder' => '',
									'maxlength' => '',
									'rows' => '',
									'new_lines' => '',
								),
								array(
									'key' => 'field_5b58ec1e99143',
									'label' => 'Result',
									'name' => 'fp_pitch_report_drumbeat_story_result',
									'type' => 'wysiwyg',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'default_value' => '',
									'tabs' => 'all',
									'toolbar' => 'full',
									'media_upload' => 1,
									'delay' => 0,
								),
							),
							'min' => '',
							'max' => '',
						),
					),
					'button_label' => 'Add Section',
					'min' => '',
					'max' => '',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'fp-pitch-reports',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => 1,
			'description' => '',
		));
		
		endif;
}
	
	// Assign Archive & Single Page Templates

	public function fp_media_contact_archive_template( $archive_template ) {
		global $post;
		if ( is_post_type_archive ( 'fp-media-contacts' ) ) {
			$archive_template = dirname( __FILE__ ) . '/archive-mediacontact.php';
		}
		return $archive_template;
	}
	
	public function fp_media_contact_single_template( $single_template ) {
		global $post;
		if ( $post->post_type == 'fp-media-contacts' ) {
			$single_template = dirname( __FILE__ ) . '/single-mediacontact.php';
		}
		return $single_template;
	}

	public function fp_pitch_archive_template( $archive_template ) {
		global $post;
		if ( is_post_type_archive ( 'fp-pitches' ) ) {
			$archive_template = dirname( __FILE__ ) . '/archive-pitch.php';
		}
		return $archive_template;
	}
	
	public function fp_pitch_single_template( $single_template ) {
		global $post;
		if ( $post->post_type == 'fp-pitches' ) {
			$single_template = dirname( __FILE__ ) . '/single-pitch.php';
		}
		return $single_template;
	}
	
	public function fp_pitch_report_archive_template( $archive_template ) {
		global $post;
		if ( is_post_type_archive ( 'fp-pitch-reports' ) ) {
			$archive_template = dirname( __FILE__ ) . '/archive-pitchreport.php';
		}
		return $archive_template;
	}
	
	public function fp_pitch_report_single_template( $single_template ) {
		global $post;
		if ( $post->post_type == 'fp-pitch-reports' ) {
			$single_template = dirname( __FILE__ ) . '/single-pitchreport.php';
		}
		return $single_template;
	}
		
	// Include Styles

	public function fp_pitch_frontend_styles() {
		if ( get_post_type() == 'fp-pitches' or get_post_type() == 'fp-pitch-reports' ) {
			wp_enqueue_style( 'fp-google-fonts', 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700', false );
			
			$pluginpath = plugin_dir_url( __FILE__ );
			wp_enqueue_style( 'style', $pluginpath . 'css/style.css', 998 );
			wp_enqueue_style( 'style-popup', $pluginpath . 'css/popup.css', 999 );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'js-magnific', $pluginpath . 'js/jquery.magnific-popup.min.js' );
			wp_enqueue_script( 'js-popup', $pluginpath . 'js/popup.js' );
			wp_enqueue_script( 'js-topbutton', $pluginpath . 'js/topbutton.js' );
		}
		if ( get_post_type() == 'fp-pitch-reports' ) {
			add_action('wp_head', 'noindex', 1);
		}

	}

}

new FP_Pitch_Plugin();