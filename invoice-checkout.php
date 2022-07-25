<?php 
/*  
* Plugin Name: Invoice Checkout
* Plugin URI:  
* Description: Add invoice choice to order checkout  
* Author: Spiros Tsikas  
* Version: 1.0.0  
* Author URI: 
* License: GPL3+  
* Text Domain:  Invoice Checkout
* Domain Path: /languages
*/



if ( in_array( 'woocommerce/woocommerce.php', get_option('active_plugins'))) {


	wp_enqueue_script( 'invoice-checkout', plugin_dir_url(__FILE__) .'public/script.js', array('jquery'), null, true);
	wp_enqueue_style('billing_fields_css', plugin_dir_url(__FILE__) .'public/style.css');

	add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
	add_filter('woocommerce_admin_billing_fields', 'ic_add_woocommerce_admin_billing_fields');
	add_filter('woocommerce_email_order_meta_fields', 'spiros_email_order_meta_fields', 20, 3 );

	add_action('woocommerce_checkout_process', 'ic_checkout_field_process');
	add_action( 'woocommerce_checkout_update_order_meta', 'spiros_save_extra_checkout_fields', 10, 2 );
	add_action('woocommerce_email_customer_details', 'spiros_show_email_order_meta', 30, 3 );

	

	


function ic_get_keys_labels( $all = true ){
	$data = [
			'timologio' => __('Issue of Invoice'),
			'vat'           => __('Company VAT number'),
			'doy'           => __('Tax Office'),
			'epag' 			=> __('Profession'),
			'addr'         => __('Business address'),
	];
	if( ! $all )
			unset($data['timologio']);

	return $data;
}

 // Hook in


// Our hooked in function - $fields is passed via the filter!
function custom_override_checkout_fields( $fields ) {


	unset($fields['billing']['billing_company']);

	$labels = ic_get_keys_labels();
	
	$fields['billing']['billing_timologio'] = array(
		'label'       => $labels['timologio'],
		'placeholder' => $labels['timologio'], 'placeholder', 'woocommerce',
		'priority'	=> 111,
		'class'       => array( 'form-row-wide', 'timologio-select' ),
		'required'    => false,
		'clear'       => false,
		'type'        => 'select',
		'options'     => array(
			'no' => __('NO', 'woocomerce' ),
			'yes' => __('YES', 'woocomerce')
			)
		);


    $fields['billing']['billing_vat'] = array(
        'label'     => $labels['vat'],
		'placeholder'   => $labels['vat'], 'placeholder', 'woocommerce',
		'class'     => array('form-row-wide', 'timologio-hide', 'validate-required'),
		'clear'     => true
		 );
	
	$fields['billing']['billing_doy'] = array(
        'label'     => $labels['doy'],
		'placeholder'   => $labels['doy'], 'placeholder', 'woocommerce',
		'class'     => array('form-row-wide', 'timologio-hide', 'validate-required'),
		'clear'     => true
		 );
	
	$fields['billing']['billing_epag'] = array(
		'label'     => $labels['epag'],
		'placeholder'   => $labels['epag'], 'placeholder', 'woocommerce',
		'class'     => array('form-row-wide', 'timologio-hide', 'validate-required'),
		'clear'     => true
	);
	
	$fields['billing']['billing_addr'] = array(
		'label'     => $labels['addr'],
		'placeholder'   => $labels['addr'], 'placeholder', 'woocommerce',
		'class'     => array('form-row-wide', 'timologio-hide', 'validate-required'),
		'clear'     => true
	);  

     return $fields;
}



function ic_add_woocommerce_admin_billing_fields($billing_fields) {

	if ( $_POST['billing_timologio'] == 'yes' ) {
		foreach ( ic_get_keys_labels() as $key => $label ) {
			$billing_fields[$key]['label'] = $label;
		}
	}

	return $billing_fields;
}


function ic_checkout_field_process() {
	if ( $_POST['billing_timologio'] == 'yes' ) {
		// Loop through the (partial) keys/labels array
		foreach( ic_get_keys_labels(false) as $key => $label ){
			// Check if set, if not avoid checkout displaying an error notice.
			if ( ! $_POST['billing_'.$key]) {
				wc_add_notice( sprintf( __('%s is a required field.', TEXT_DOMAIN ), $label ), 'error' );
			}
		}
	}
}


function spiros_save_extra_checkout_fields( $order_id, $posted ){

	if( isset( $posted['billing_timologio'] ) ) {
		update_post_meta( $order_id, '_billing_timologio', sanitize_text_field( $posted['billing_timologio'] ) );
	}

	if( isset( $posted['billing_vat'] ) ) {
		update_post_meta( $order_id, '_billing_vat', sanitize_text_field( $posted['billing_vat'] ) );
	}
	if( isset( $posted['billing_doy'] ) ) {
		update_post_meta( $order_id, '_billing_doy', sanitize_text_field( $posted['billing_doy'] ) );
	}
	if( isset( $posted['billing_epag'] ) ) {
		update_post_meta( $order_id, '_billing_epag', sanitize_text_field( $posted['billing_epag'] ) );
	}
	if( isset( $posted['billing_addr'] ) ) {
		update_post_meta( $order_id, '_billing_addr', sanitize_text_field( $posted['billing_addr'] ) );
	}

}


function spiros_email_order_meta_fields( $fields, $sent_to_admin, $order ) {

	$fields['timologio'] = array(

		'label' => __( 'Issue of Invoice' ),
		
		'value' => get_post_meta( $order->id, '_billing_timologio', true ),
		
		);

	$fields['timologio_vat'] = array(

		'label' => __( 'Company VAT number' ),
		
		'value' => get_post_meta( $order->id, '_billing_vat', true ),
		
		);
	$fields['timologio_doy'] = array(

		'label' => __( 'Tax Office' ),
		
		'value' => get_post_meta( $order->id, '_billing_doy', true ),
		
		);
	$fields['timologio_epag'] = array(

		'label' => __( 'Profession' ),
		
		'value' => get_post_meta( $order->id, '_billing_epag', true ),
		
		);
	$fields['timologio_addr'] = array(

		'label' => __( 'Business address' ),
		
		'value' => get_post_meta( $order->id, '_billing_addr', true ),
		
		);
}


function spiros_show_email_order_meta( $order, $sent_to_admin, $plain_text) {

	$option = get_post_meta( $order->id, '_billing_timologio', true );

	if($option === 'yes') {
		$emailPrint .= '<h3>' . __('Invoice') . '</h3>';
		$emailPrint .= '<table style="border:1px solid #000">';
		$emailPrint .= '<tr>';
		$emailPrint .= '<td><strong> ' . __('Company VAT number:') . ' </strong></td><td>' . get_post_meta( $order->id, '_billing_vat', true ) .'</td>';
		$emailPrint .= '</tr>';
		$emailPrint .= '<tr>';
		$emailPrint .= '<td><strong>' . __('Tax Office:') . '</strong></td><td>' . get_post_meta( $order->id, '_billing_doy', true ) .'</td>';
		$emailPrint .= '</tr>';
		$emailPrint .= '<tr>';
		$emailPrint .= '<td><strong> ' . __('Profession:') . ' </strong></td><td>' . get_post_meta( $order->id, '_billing_epag', true ) .'</td>';
		$emailPrint .= '</tr>';
		$emailPrint .= '<tr>';
		$emailPrint .= '<td><strong> ' . __('Business address:') . '</strong></td><td>' . get_post_meta( $order->id, '_billing_addr', true ) .'</td>';
		$emailPrint .= '</tr>';
		$emailPrint .= '</table>';
	
		echo $emailPrint;
	}

}

}

?>