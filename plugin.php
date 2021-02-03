<?php
/*
    Plugin Name: Variable Product Price Prefix
    Description: Change price display for variable products from '$10-$20' to 'Starts from $10'
    Version: 1.0.0
    Author: SF-Solutions
	Author URI: http://www.sf-solutions.net/
*/

//Settings page
function vppp_register_settings() {
    add_option( 'vppp_option_name', 'Starts From');
    register_setting( 'vppp_options_group', 'vppp_option_name', 'vppp_callback' );
}
add_action( 'admin_init', 'vppp_register_settings' );

function vppp_register_options_page() {
    add_options_page('Variable Product Price Prefix', 'Variable Product Price Prefix', 'manage_options', 'vppp', 'vppp_options_page');
}
add_action('admin_menu', 'vppp_register_options_page');

function vppp_options_page(){
?>
    <div>
        <?php screen_icon(); ?>
        <h2>Variable Product Price Prefix</h2>
        <form method="post" action="options.php">
            <?php settings_fields( 'vppp_options_group' ); ?>
                <table>
                    <tr valign="top">
                        <th scope="row"><label for="vppp_option_name">Price Prefix</label></th>
                        <td><input type="text" id="vppp_option_name" name="vppp_option_name" value="<?php echo get_option('vppp_option_name'); ?>" /></td>
                    </tr>
                </table>
            <?php  submit_button(); ?>
        </form>
    </div>
<?php
}

//Add prefix
add_filter( 'woocommerce_variable_sale_price_html', 'vppp_variation_price_format', 10, 2 );
 
add_filter( 'woocommerce_variable_price_html', 'vppp_variation_price_format', 10, 2 );
 
function vppp_variation_price_format( $price, $product ) {
    // Main Price
    $prices = [
        $product->get_variation_price( 'min', true ), 
        $product->get_variation_price( 'max', true ) 
    ];

    $price = $prices[0] !== $prices[1] ? sprintf( __( '%1$s %2$s', 'woocommerce' ), get_option('vppp_option_name'), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
    
    // Sale Price
    $prices = [
        $product->get_variation_regular_price( 'min', true ), 
        $product->get_variation_regular_price( 'max', true ) 
    ];
    sort( $prices );
    $saleprice = $prices[0] !== $prices[1] ? sprintf( __( '%1$s %2$s', 'woocommerce' ), get_option('vppp_option_name'), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
    
    if ( $price !== $saleprice ) {
        $price = '<del>' . $saleprice . '</del> <ins>' . $price . '</ins>';
    }
    return $price;
}