<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */
?>
<!-- <?php
$images// = array(
         // get_field('gallery'));
//$size// = 'full'; // (thumbnail, medium, large, full or custom size)
//if( $images ): ?>
        <?php //foreach( $images as $image_id ): ?>
                <?php //echo wp_get_attachment_image( $image_id, $size ); ?>
        <?php //endforeach; ?>
<?php //endif; ?> -->
<?php  

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

echo wc_get_stock_html( $product ); // WPCS: XSS ok.

if ( $product->is_in_stock() ) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
		<?php
    
		function get_custom_field($ptype){
		$dropdown = '<select class="'.$ptype.'" name="'.$ptype.'">';
          if($ptype=="flavor"){
          $dropdown .= '<option value="" selected>Please Select Flavour</option>';
          }
          else if($ptype=="pizza_size"){
          $dropdown .= '<option value="" selected>Please Select Size</option>';
          }
			/*$dropdown = '<select class="'.$ptype.'">';*/
		    $args = array(
		    	"post_type" => $ptype);
		    $post_data = get_posts($args);
        
        	 foreach ($post_data as $key => $value){
        	 	if($value->ID){
        	 		$title = $value->post_title;
        	 		$price = get_field("price", $value->ID);
        	 		$dropdown .= '<option value="'.$title.'-'.$price.'" name="'.$title.'" >'.$title.'</option>';
        	 	}
        	 }
        	 $dropdown .= '</select>';
        	 return $dropdown;
        	}


function get_addons($ptype)
{
  if(!empty($ptype))
  {
     $args = array(
       "post_type" => $ptype);
        $addons = get_posts($args);
        $html = '';
    foreach ($addons as $key => $value)
      {
          if(!empty($ptype))
            {
              $label = $value->post_title;
              /*$lname = strtolower(str_replace(" ","_",$label));*/
              $price = get_field("price", $value->ID);
              $html .= '<div class="addon  '.$label.'">';
              $html .= '<div class="label"> '.$label.'-  $'.$price.'</div>';
              $html .= '<div class="container"><button class="plus" type="button" value="+">+</button>
              <input type="number" name="quantity-'.$label.'" max="6" min="1" Value="0" data="'.$price.'" readonly>
              <button class="minus" type="button" value="-">-</button> </div>';
              $html .='<div class="aop"><span name="'.$label.'" class="aop"  data="0" value="0"></span>
              <input class="adon-price" type="hidden" value="'.$price.'"  name="'.$label.'_price" readonly >
              </div>';
              $html .= '</div>';
            }
      }
      return $html;
  }
  return '';
}
        	
        		
            ?>
            <div>
            	<div>
            	<div class="col-md-1 label">
            		<label>Flavours</label>
            	</div>
            	<div class="opt">
            		<?php echo get_custom_field("flavor");  ?>
            	</div>
            	<div class="fp">
            		<span class="fp" data="0"></span>
            	</div>
            	</div>
            	<div>
            	<div class="col-md-1 label">
            		<label>Pizza Sizes</label>
            	</div>
            	<div class="opt">
            		<?php echo get_custom_field("pizza_size");  ?>
            	</div>
            	<div class="sp">
            		<span class="sp"  data="0"></span>
            	</div>
            	</div>
            	<div>
            	<div class="col-md-1 label">
            		<label>Add_Ons</label>
                <?php echo get_addons("add_ons");  ?>
            	</div>
            	</div>
            	<div class="total">
            	<label>Total - </label>
            	<span class="tprice">
            	</span>
            	</div>
            </div>

		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<?php
		do_action( 'woocommerce_before_add_to_cart_quantity' );

		woocommerce_quantity_input(
			array(
				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
				'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
			)
		);

		do_action( 'woocommerce_after_add_to_cart_quantity' );
		?>

		<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>
  <script type="text/javascript">
      jQuery(document).ready(function(){


    jQuery("select.flavor").change(function(){
           var optValue = jQuery(this).val();
           splitValue = optValue.split("-");
           jQuery('span.fp').text("$"+splitValue[1]);
           jQuery('span.fp').attr("data",splitValue[1]);
           calculateTotal();
    });


    jQuery("select.pizza_size").change(function(){
           var optValue = jQuery(this).val();
           splitValue = optValue.split("-");
           jQuery('span.sp').text("$"+splitValue[1]);
           jQuery('span.sp').attr("data",splitValue[1]);
           calculateTotal();
    });

  });
jQuery(".plus").click(function() {
    var a = jQuery(this).next().val();
    jQuery(this).next().val(++a);
    var num = jQuery(this).next().val();
  var price = jQuery(this).next().attr("data");
  var sum = parseInt(num) * parseInt(price);
   jQuery(this).parent("div.container").next().children().text("$"+ sum);
   jQuery(this).parent("div.container").next().children().attr("data", sum);
   calculateTotal();
 });
jQuery(".minus").click(function() {
    var a = jQuery(this).prev().val();
    if(a>0){
    jQuery(this).prev().val(--a);
    var num = jQuery(this).prev().val();
  var price = jQuery(this).prev().attr("data");
  var sum = parseInt(num) * parseInt(price);
  jQuery(this).parent("div.container").next().children().text("$"+ sum);
   jQuery(this).parent("div.container").next().children().attr("data", sum);
   calculateTotal();
   }
 });
/*  jQuery(".plus").click(function() {
    var $n = jQuery(this)
    .parent("div.opt")
    .find("input[name=quantity]");
  $n.val(Number($n.val())+1 );
   var num = jQuery(this).next().val();
  var price = jQuery(this).next().attr("data");
jQuery("div.aop").children().val(parseInt(num) * parseInt(price));
});
  jQuery(".minus").click(function() {
   var $n = jQuery(this)
    .parent("div.opt")
    .find("input[name=quantity]");
  var amount = Number($n.val());
  if (amount > 0) {
    $n.val(amount-1);
  }

});*/


  function calculateTotal(){
    var fprice = jQuery("span.fp").attr("data"); 
    var sprice = jQuery("span.sp").attr("data");
    var sum = 0; 
    jQuery( "span.aop" ).each(function( index, element ) {
     var data = jQuery(element).attr("data");
      sum = sum + parseInt(data);
        });
    var total = parseInt(fprice) + parseInt(sprice) + sum;
    jQuery("span.tprice").text("$"+total);
  }

  function checkValidation(){
    var fval = jQuery("select.flavour").val();
    var sval = jQuery("select.pizza_size").val();
    if(!(fval && sval)){
        
        event.preventDefault();
        alert("Please select flavour & size of pizza");
    }
  } 

  </script>