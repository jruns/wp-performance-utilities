<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/jruns/wp-performance-utilities
 * @since      0.1.0
 *
 * @package    PerformanceUtilities
 * @subpackage PerformanceUtilities/admin/partials
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$perfutils_settings = (array) get_option( 'perfutils_settings', array() );
?>

<div class="wrap">
<h1><?php esc_html_e( 'Performance Utilities', 'performance-utilities' ); ?></h1>

<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
<?php settings_fields( 'performance-utilities' ); ?>

<ul>
<li class="itemDetail">
<h2 class="itemTitle"><?php esc_html_e( 'General Utilities', 'performance-utilities' ); ?></h2>

<table class="form-table">
<?php
$args = array(
    'name'              => 'disable_jquery_migrate',
    'heading'           => 'Disable jQuery Migrate',
    'description'       => 'Disable jQuery migrate script from the frontend.'
);
perfutils_output_admin_option( $args, $perfutils_settings );

$args = array(
    'name'              => 'remove_versions',
    'heading'           => 'Remove Versions from Scripts and Styles',
    'description'       => 'Remove versions from the source urls of external scripts and styles on the frontend. This can improve browser and CDN caching.'
);
perfutils_output_admin_option( $args, $perfutils_settings );
?>
</table>


<h2 class="itemTitle"><?php esc_html_e( 'Facade Utilities', 'performance-utilities' ); ?></h2>

<table class="form-table">
<?php
$args = array(
    'name'              => 'enable_youtube_facade',
    'heading'           => 'Enable YouTube Facade',
    'description'       => 'Enable YouTube facade for videos on the frontend, and delay loading videos until the user clicks the placeholder image.'
);
perfutils_output_admin_option( $args, $perfutils_settings );
?>
</table>


<h2 class="itemTitle"><?php esc_html_e( 'Largest Contentful Paint Utilities', 'performance-utilities' ); ?></h2>

<table class="form-table">
<?php
$args = array(
    'name'              => 'preload_images',
    'heading'           => 'Preload Images',
    'description'       => 'Enable the <code>perfutils_images_to_preload</code> WordPress filter to selectively preload images on the frontend. <a href="https://github.com/jruns/wp-performance-utilities/wiki/Preload-Images" target="_blank">Learn how to use the filter</a>.'
);
perfutils_output_admin_option( $args, $perfutils_settings );
?>
</table>


<h2 class="itemTitle"><?php esc_html_e( 'Render-Blocking Utilities', 'performance-utilities' ); ?></h2>

<table class="form-table">
<?php
$args = array(
    'name'              => 'move_scripts_and_styles_to_footer',
    'heading'           => 'Move Scripts and Styles to Footer',
    'description'       => 'Enable the <code>perfutils_scripts_and_styles_to_move_to_footer</code> WordPress filter to selectively move scripts and styles to the page footer on the frontend. <a href="https://github.com/jruns/wp-performance-utilities/wiki/Move-Scripts-and-Styles-to-Footer" target="_blank">Learn how to use the filter</a>.'
);
perfutils_output_admin_option( $args, $perfutils_settings );

$args = array(
    'name'              => 'remove_scripts_and_styles',
    'heading'           => 'Remove Scripts and Styles',
    'description'       => 'Enable the <code>perfutils_scripts_and_styles_to_remove</code> WordPress filter to selectively remove scripts and styles from the frontend. <a href="https://github.com/jruns/wp-performance-utilities/wiki/Remove-Scripts-and-Styles" target="_blank">Learn how to use the filter</a>.'
);
perfutils_output_admin_option( $args, $perfutils_settings );

$args = array(
    'name'              => 'delay_scripts_and_styles',
    'heading'           => 'Delay Scripts and Styles',
    'description'       => 'Enable the <code>perfutils_scripts_and_styles_to_delay</code> WordPress filter to selectively delay javascript and stylesheets on the frontend. <a href="https://github.com/jruns/wp-performance-utilities/wiki/Delay-Scripts-and-Stylesheets" target="_blank">Learn how to use the filter</a>.',
    'child_options'     => array(
        array(
            'name'              => 'autoload_delay',
            'type'              => 'number',
            'default'           => 15000,
            'heading'           => 'User interaction autoload delay',
            'description'       => 'Modify the autoload delay that will load a script when the user has not yet interacted with the page. Default is 15 seconds (in milliseconds).'
        )
    )
);
perfutils_output_admin_option( $args, $perfutils_settings );
?>
</table>

</li>
</ul>

<p class="submit">
<input type="submit" class="button-secondary" value="<?php esc_html_e( 'Save Changes', 'performance-utilities' ); ?>" />
</p>

</form>
</div>

<?php

function perfutils_output_admin_option( $args, $perfutils_settings, $should_return = false ) {
    $parent = $args['parent'] ?? null;
    $type = $args['type'] ?? '';
    $name = $args['name'] ?? '';
    $heading = $args['heading'] ?? '';
    $description = $args['description'] ?? '';
    $default = $args['default'] ?? '';
    $child_options = $args['child_options'] ?? array();

    $utility_constant = strtoupper( 'perfutils_' . ( $parent ? $parent . '_' : '' ) . $name );
    $utility_value = null;
    $placeholder = '';
    $after_label_msg = '';
    if( defined( $utility_constant ) ) {
        $utility_value = constant( $utility_constant );
        $after_label_msg = "<span class='tooltip'><span class='dashicons dashicons-warning'></span><span class='tooltip-text'>This setting is currently configured in your wp-config.php file and can only be enabled or disabled there.<br/><br/>Remove $utility_constant from wp-config.php in order to enable/disable this setting here.</span></span>";
    } else if ( ! empty( $perfutils_settings ) ) {
        if ( ! empty( $parent ) && array_key_exists( $parent, $perfutils_settings ) && array_key_exists( $name, $perfutils_settings[$parent] ) ) {
            $utility_value = $perfutils_settings[$parent][$name];
        } else if ( array_key_exists( 'active_utilities', $perfutils_settings ) && array_key_exists( $name, $perfutils_settings['active_utilities'] ) ) {
            $utility_value = $perfutils_settings['active_utilities'][$name];
        }

        if ( is_numeric( $utility_value ) || ( is_array( $utility_value ) && ! empty( $utility_value ) ) ) {
            $utility_value = absint( $utility_value );
        } else {
            $utility_value = sanitize_text_field( $utility_value );
        }
    }

    $child_output = '';

    if ( ! empty( $child_options ) && is_array( $child_options ) ) {
        foreach( $child_options as $child ) {
            $child['parent'] = $name;
            $child_output .= perfutils_output_admin_option( $child, $perfutils_settings,  true );
        }
        $child_output = "<table class='child-table'>" . $child_output . "</table>";
    }

    $form_field_name = "perfutils_settings" . ( $parent ? "[$parent]" : "[active_utilities]" ). "[$name]";

    $input_output = "<input type='checkbox' name='$form_field_name' value='1' " . ( $utility_value ? "checked='checked'" : '' ) . ( defined( $utility_constant ) ? " disabled='' title='Remove $utility_constant from wp-config.php in order to configure this setting here.'" : "" ) . "/>" . $description . "$after_label_msg";
    if ( ! empty( $type ) ) {
        if ( empty( $utility_value ) && ! empty( $default ) ) {
            $placeholder = "placeholder='$default'";
        }

        if ( 'number' === $type ) {
            $input_output = $description . "<br/><input type='number' name='$form_field_name' value='$utility_value' $placeholder" . ( defined( $utility_constant ) ? " disabled='' title='Remove $utility_constant from wp-config.php in order to configure this setting here.'" : "" ) . "/>$after_label_msg";
        }
    }

    $allowed_html = array(
        'table' => array(
			'class' => array(),
        ),
        'tr' => array(
			'valign' => array(),
        ),
        'th' => array(
			'scope' => array(),
        ),
        'td' => array(),
        'label' => array(),
		'input' => array(
			'type' => array(),
			'id' => array(),
			'name' => array(),
			'value' => array(),
			'placeholder' => array(),
			'checked' => array(),
			'disabled' => array(),
		),
		'span' => array(
			'class' => array(),
		),

		'a' => array(
			'href' => array(),
			'target' => array(),
		),
        'code' => array(),
        'p' => array(),
        'br' => array(),
    );

    $output = "<tr valign='top'>
        <th scope='row'>" . $heading . "</th>" .
        ( ! empty( $parent ) ? "</tr><tr valign='top'>" : "" ) .
        "<td><label>$input_output</label>
        $child_output
        </td></tr>";

    if ( $should_return ) {
        return $output;
    } else {
        echo wp_kses( $output, $allowed_html );
    }
}