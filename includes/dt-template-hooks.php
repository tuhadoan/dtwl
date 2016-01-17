<?php
/**
 * DT WooCommerce Layouts Template Hooks
 *
 * Action/filter hooks used for functions/templates
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 * Product loop add action
 */
add_action('dtwl_template_loop_item_action', 'dtwl_template_loop_add_action', 10);
