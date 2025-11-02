<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class PerformanceUtilities_Preload_Images {

	private $settings;

	public static $needs_html_buffer = false;
	public static $runs_in_admin = true;

	public function __construct() {
		$this->settings = array(
			'images'	=> array()
		);

		$this->settings = apply_filters( 'perfutils_images_to_preload', $this->settings ) ?? $this->settings;
	}

	public function filter_settings() {
		// Filter out settings that are not valid for the current page, based on conditional matches
		$this->settings['images'] = PerformanceUtilities_Conditional_Checks::filter_matches( $this->settings['images'] );
	}

	public function add_preload_tags() {
		global $post;

		$meta_values = array();
		
		if ( is_singular() ) {
			$meta_values = get_post_meta( $post->ID, '_perfutils_preload_images', true );
		}

		if ( ! empty( $meta_values ) || ! empty( $this->settings['images'] ) ) {
			// Process images to preload
			$preload_tags = '';

			$defaults = array(
				'image1' => array(),
				'image2' => array(),
				'image3' => array(),
			);
			$meta_values = wp_parse_args( $meta_values, $defaults );

			$images_array = array_merge( $this->settings['images'], $meta_values );

			foreach( $images_array as $image_setting ) {
				if ( array_key_exists( 'url', $image_setting ) && ! empty( $image_setting['url'] ) ) {
					$media_query = '';
					$url = esc_url( $image_setting['url'] );

					if ( array_key_exists( 'args', $image_setting ) && ! empty( $image_setting['args'] ) && array_key_exists( 'media', $image_setting['args'] ) && ! empty( $image_setting['args']['media'] ) ) {
						// Image from WP filter
						$media_query = "media=\"" . trim( $image_setting['args']['media'] ) . "\" ";
					} else if ( array_key_exists( 'comparison', $image_setting ) && array_key_exists( 'width', $image_setting ) && ! empty( $image_setting['width'] ) ) {
						// Image from meta box
						$width = sanitize_text_field( $image_setting['width'] );
						if ( is_numeric( $width ) ) {
							$width = "{$width}px";
						}
						$comparison = sanitize_key( $image_setting['comparison'] );

						switch( $comparison ) {
							case 'eq':
								$comparison = '=';
								break;
							case 'lteq':
								$comparison = '<=';
								break;
							case 'lt':
								$comparison = '<';
								break;
							case 'gteq':
								$comparison = '>=';
								break;
							case 'gt':
							default:
								$comparison = '>';
								break;
						}
						$media_query = "media=\"(width $comparison $width)\" ";
					}
					$preload_tags = $preload_tags . "<link rel=\"preload\" href=\"{$url}\" as=\"image\" fetchpriority=\"high\" {$media_query}/>" . PHP_EOL;
				}
			}

			echo $preload_tags;
		}
	}

	public function add_meta_box( $post_type, $post ) {

		register_meta(
			'post',
			'_perfutils_preload_images',
			array(
				'type' => 'array',
				'description' => 'preloaded images',
				'single' => true,
				'show_in_rest' =>  array(
					'schema' => array(
						'type' => 'array',
						'items' => array(
                     		'type' => 'array',
                 		),
						'default' => array()
					)
				),
				'sanitize_callback' => array( $this, 'sanitize_meta_value' ),
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				}
			)
		);

		add_meta_box(
			'perfutils_preload_images_metabox',
			'Preload Images',
			array( $this, 'render_meta_box' ),
			null,
			'normal',
			'low',
			array( '__block_editor_compatible_meta_box' => true )
		);
	}

	public function sanitize_meta_value( $meta_value, $meta_key, $object_type, $object_subtype ) {
		return map_deep( $meta_value, 'sanitize_text_field' );
	}
	
	public function render_meta_box( $post ) {
		$values = get_post_meta( $post->ID, '_perfutils_preload_images', true );

		$defaults = array(
			'image1' => array(),
			'image2' => array(),
			'image3' => array(),
		);
  		$values = wp_parse_args( $values, $defaults );

		wp_enqueue_style( 'perfutils-preload-images-editor', plugin_dir_url( __DIR__ ) . 'css/preload_images_editor.css', array(), constant( 'PERFUTILS_VERSION' ) );

    	$allowed_html = array(
			'div' => array(
				'class' => array(),
				'style' => array()
			),
			'input' => array(
				'name' => array(),
				'id' => array(),
				'class' => array(),
				'type' => array(),
				'size' => array(),
				'placeholder' => array(),
				'value' => array(),
			),
			'select' => array(
				'name' => array(),
				'id' => array(),
				'class' => array(),
			),
			'option' => array(
				'value' => array(),
				'selected' => array(),
			),
			'br' => array()
		);

		$output = '
		<div class="perfutils-preloadimages perfutils-container">
			<div class="perfutils-row">
				<div class="perfutils-item"></div>
				<div class="perfutils-item perfutils-bold">Image URL:</div>
				<div class="perfutils-item perfutils-bold">Load when Screen Width is:</div>
			</div>' . PHP_EOL .
			$this->render_meta_box_image_row( 1, $values['image1'] ) . PHP_EOL .
			$this->render_meta_box_image_row( 2, $values['image2'] ) . PHP_EOL .
			$this->render_meta_box_image_row( 3, $values['image3'] ) . PHP_EOL .
		'</div>';

		wp_nonce_field( 'perfutils_preloadimages_metabox', 'perfutils_preloadimages_metabox_nonce' );
		echo wp_kses( $output, $allowed_html );
	}

	public function render_meta_box_image_row( $id, $values ) {
		$url = esc_url( $values['url'] ?? '' );
		$comparison = sanitize_key( $values['comparison'] ?? '' );
		$width = sanitize_text_field( $values['width'] ?? '' );

		if ( ! empty( $width ) ) {
			if( is_numeric( $width ) ) {
				$width = $width . 'px';
			}
		}

		$output = "<div class='perfutils-row'>
				<div class='perfutils-item perfutils-bold'>Image $id:</div>
				<div class='perfutils-item'><input name='perfutils_preloadimages[image$id][url]' id='perfutils_preloadimages[image$id][url]' type='text' placeholder='Enter the image URL' class='fullwidth' value='$url' /></div>
				<div class='perfutils-item'>
					<select name='perfutils_preloadimages[image$id][comparison]' id='perfutils_preloadimages[image$id][comparison]'>
						<option value=''>-- Comparison operator --</option>
						<option value='gt' " . selected( $comparison, 'gt', false ) . ">Greater than</option>
						<option value='lt' " . selected( $comparison, 'lt', false ) . ">Less than</option>
						<option value='gteq' " . selected( $comparison, 'gteq', false ) . ">Greater than or equal to</option>
						<option value='lteq' " . selected( $comparison, 'lteq', false ) . ">Less than or equal to</option>
						<option value='eq' " . selected( $comparison, 'eq', false ) . ">Equal to</option>
					</select>
					<input name='perfutils_preloadimages[image$id][width]' id='perfutils_preloadimages[image$id][width]' type='text' size='7' placeholder='Width in px' value='$width' />
				</div>
			</div>";
		
		return $output;
	}

	public function save_metabox_data( $post_id ) {
		if ( null === $post_id) {
			return false;
		}

		if ( ! array_key_exists( 'perfutils_preloadimages_metabox_nonce', $_POST ) || ! wp_verify_nonce( wp_unslash( $_POST['perfutils_preloadimages_metabox_nonce'] ), 'perfutils_preloadimages_metabox' ) ) {
			return false;
		}

		if ( array_key_exists( 'perfutils_preloadimages', $_POST ) && is_array( $_POST['perfutils_preloadimages'] ) ) {
			$values = map_deep( wp_unslash( $_POST['perfutils_preloadimages'] ), 'sanitize_text_field' );

			update_post_meta(
				$post_id,
				'_perfutils_preload_images',
				$values
			);
		}
	}

	/**
	 * Execute commands after initialization
	 *
	 * @since    1.0.0
	 */
	public function run() {
		add_filter( 'wp', array( $this, 'filter_settings' ) );
		add_action( 'wp_head', array( $this, 'add_preload_tags' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 100, 2 );
		add_action( 'save_post', array( $this, 'save_metabox_data' ) );
	}
}