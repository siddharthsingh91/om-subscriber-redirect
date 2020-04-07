<?php 
/**
 * Plugin Name: Om Change Subscribe
 * Plugin URI: http://sanditsolution.com/
 * Description: Redirecting user to thanks page if they have active membership. 
 * Version: 1.0
 * Author: Siddharth Singh
 * Author URI: http://sanditsolution.com/
 * License: A "Slug" license name e.g. GPL2
 */
add_action( 'woocommerce_single_product_summary', 'om_unavailable_product_display_message', 20 );
function om_unavailable_product_display_message() {
    global $product; 
    $product_id = $product->get_id();
    $user = wp_get_current_user();
    $user_id = $user->ID;
    $customer_email = $user->user_email;
    if(wc_customer_bought_product( $customer_email, $user_id, $product_id )){
        $url = "https://s2scrossfit.com/thank-you-for-your-booking/";
        echo("<script>location.href = '".$url."'</script>");
        wp_redirect( $url );
        exit;
		}
}


function om_products_bought_curr_user() {
   
    // GET CURR USER
    $current_user = wp_get_current_user();
    if ( 0 == $current_user->ID ) return;
   
    // GET USER ORDERS (COMPLETED + PROCESSING)
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => $current_user->ID,
        'post_type'   => wc_get_order_types(),
        'post_status' => array_keys( wc_get_is_paid_statuses() ),
    ) );
    
  
    // LOOP THROUGH ORDERS AND GET PRODUCT IDS
    if ( ! $customer_orders ) return;
    $product_ids = array();
    foreach ( $customer_orders as $customer_order ) {
        $order = wc_get_order( $customer_order->ID );
        $items = $order->get_items();
        foreach ( $items as $item ) {
            $product_id = $item->get_product_id();
            $product_ids[] = $product_id;
        }
    }
    return $product_ids; }


add_action('wp_head','om_head_check');
function om_head_check(){
    global $product; 
    
	echo '<style>.wc-bookings-availability-navigation-next{visibility:hidden !important;}</style>';
	
    $user_products_id = om_products_bought_curr_user();
    $open_gym_products = om_category_products('open-gym');
    $open_personal_training = om_category_products('personal-training');
    $open_unlimited_membership = om_category_products('unlimited-membership');

    
$array_result_open_gym = count(array_intersect($user_products_id, $open_gym_products));
$array_personal_training = count(array_intersect($user_products_id, $open_personal_training));
$array_unlimited_membership = count(array_intersect($user_products_id, $open_unlimited_membership));

$array_unlimited_memberships = array_intersect($user_products_id, $open_unlimited_membership);

if (is_page(array('booking-page','pt-booking-page'))) :   
if (!is_user_logged_in()) {
    om_redirect("https://s2scrossfit.com/select-membership/"); }
endif;

if (is_user_logged_in()) {
//    
    if (is_page(array('booking-page'))) { 
    if(($array_result_open_gym = 0) || ($array_unlimited_membership = 0)){
    om_redirect("https://s2scrossfit.com/select-membership/");} }
    if (is_page(array('pt-booking-page')))  {
    if(($array_personal_training = 0) || ($array_unlimited_membership = 0)){
        om_redirect("https://s2scrossfit.com/select-membership/");
     }}



     if (is_page(array('new-timetable-page','new-timetable-page-2'))) :
      
       //Below checking do user have any running subscreption  
        if(($array_result_open_gym = 0) || ($array_unlimited_membership = 0) || ($array_personal_training = 0)){

        echo ('<script>jQuery(document).ready(function(e){jQuery(".wp-block-button").html(\'<a href="https://s2scrossfit.com/thank-you-for-your-booking/" class="wp-block-button__link">Book now</a>\');});</script>');
		 
        echo ('<script>jQuery(document).on("click", ".wc-bookings-availability-navigation-prev,.wc-bookings-availability-navigation-next", function(){jQuery(".wp-block-button").html("<a href="https://s2scrossfit.com/thank-you-for-your-booking/" class="wp-block-button__link">Book now</a>");});</script>');
        
		//Below checking if user do not have running subscreption 
    }else{
		
		     echo ('<script>jQuery(document).ready(function(e){jQuery(".wp-block-button").html(\'<a href="https://s2scrossfit.com/select-membership/" class="wp-block-button__link">Book now</a>\');});
        </script>');

        echo ('<script>jQuery(document).on("click", ".wc-bookings-availability-navigation-prev,.wc-bookings-availability-navigation-next", function(){jQuery(".wp-block-button").html("<a href="https://s2scrossfit.com/select-membership/" class="wp-block-button__link">Book now</a>");});</script>');
	    //End checking the subscreption	
		}

    endif;
        
        
         
        //If end Login Check end
        }else{
        
        if (is_page(array('new-timetable-page','new-timetable-page-2'))) :   
        echo ('<script>jQuery(document).ready(function(e){jQuery(".wp-block-button").html(\'<a href="https://s2scrossfit.com/select-membership/" class="wp-block-button__link">Book now</a>\');});
        </script>');

        echo ('<script>jQuery(document).on("click", ".wc-bookings-availability-navigation-prev,.wc-bookings-availability-navigation-next", function(){jQuery(".wp-block-button").html("<a href="https://s2scrossfit.com/select-membership/" class="wp-block-button__link">Book now</a>");});</script>');
       
       endif;


       //Ifelse end   Check end
        }

}


function om_category_products($category_name){
    $products = wc_get_products(array(
        'category' => array($category_name),
    ));
    $products_id;
    foreach($products as $product){
        $products_id[] = $product->id; }
   return $products_id; } 
   
   function om_redirect($url){
    echo ("<script>location.href = '".$url."'</script>");
    wp_redirect( $url );
    exit; }  ?>