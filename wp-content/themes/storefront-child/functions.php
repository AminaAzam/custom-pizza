<?php
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
 
    $parent_style = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
 
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}

function validate_price($post_type, $name, $user_selected_price){
	$price=0;
	$qurey = new WP_Query(
		array(
			'post_type' => $post_type,
			'post_status' => array('publish')
			));
			foreach ($qurey->posts as $key => $value) {
				if($value->post_title==$name){
					$id  = $value->ID;
					$price = get_field("price", $id);
					break;
				}
			}
			if($price!=$user_selected_price){
				return false;
			}
			return true;
		
}

function validation_of_custom_data($passed, $product_id, $quantity){
	$passed = true;
	$flavor = explode("-", $_POST["flavor"]);
	$pizzasize = explode("-", $_POST["pizza_size"]);
	$passed = validate_price("flavor", $flavor[0], $flavor[1]);
	/*print_r($product_id);
	echo "<pre>";
	print_r($_POST);
	exit();*/
	return $passed;
}
add_filter('woocommerce_add_to_cart_validation','validation_of_custom_data',10, 3);

function add_custom_data_to_cart_item($cart_item_data, $product_id, $variation_id, $quantity){
	/*print_r($_POST);*/
	if($product_id == 18){

	$cart_item_data["pizza info"] = array();
	foreach ($_POST as $key => $value) {
		$cart_item_data["pizza info"][$key] = $value;
	}	
	}
	
	/*print_r($quantity);*/
	
	return $cart_item_data;
}

add_filter('woocommerce_add_cart_item_data','add_custom_data_to_cart_item',10,4);

function get_custom_data_from_cart($item_data,$cart_item){
/*echo("<pre>");
print_r($cart_item);
exit();
	*/
	if(!empty($cart_item['pizza info'])){
		foreach ($cart_item['pizza info'] as $key => $value) {

			$item = array(
			'key' => $key,
			'value' => $value);
			array_push($item_data, $item);
		}
		
		return $item_data;
	}
	return $item_data;
	
}
add_filter('woocommerce_get_item_data','get_custom_data_from_cart',10,2);

function add_custom_data_to_line_item($item, $cart_item_key, $values, $order){
    //echo ("<pre>");
	//print_r($item);
	if($values["pizza info"]){
		foreach ($values["pizza info"] as $key => $value) {
			 $item->add_meta_data($key,$value);
		}
	}
	return;
	/*print_r($values);
	print_r($order);
	exit();*/
}

add_action('woocommerce_checkout_create_order_line_item','add_custom_data_to_line_item',10,4);

function calculate_custom_total($obj){
	if(!empty($obj->cart_contents)){
		$cart_contents = $obj->cart_contents;
		foreach ($cart_contents as $key => $value) {
		/*	print_r("<pre>");
		print_r($value);*/
		if($value["pizza info"]){
			$flavor_price = explode("-", $value["pizza info"]["flavor"])[1];
			$size_price = explode("-", $value["pizza info"]["pizza_size"])[1];
			$addon_array = array(
				"quantity-Mushroom",
				"quantity-Cheese",
				"quantity-Veggies"
			);
			$sum = 0;
			foreach ($addon_array as $addonkey => $addonvalue) {
				$quantity = $value["pizza info"][$addonvalue];
				$price = $value["pizza info"][explode("-", $addonvalue)[1]."_price"];
				/*print_r($price);
				exit();*/
				$sum = $sum + (int)($quantity * $price);
			}
			$sub_total = $flavor_price + $size_price + $sum;
			$total = $sub_total;
			$value["data"]->set_price($total);

		}
		
		/*exit();*/
	}
		}
		/*print_r("<pre>");
		print_r($cart_contents);
		exit();*/
	
	/*echo("<pre>");
	print_r($obj);
	exit();*/
} 
add_action('woocommerce_before_calculate_totals','calculate_custom_total',10,1);
?>