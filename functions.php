<?php
function theme_enqueue_styles() {

    $parent_style = 'storefront-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( $parent_style ));
    wp_enqueue_style( 'main-style', get_stylesheet_directory_uri() . '/assets/css/main.min.css', array( $parent_style ));

}

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

//========================================
//Nível de Usuário Revendedor
//========================================
/*$usuario_generico_capabilities = array(
  'read'              => true,
  'edit_posts'        => false,
  'edit_pages'        => false,
  'edit_others_posts' => false,
  'create_posts'      => false,
  'manage_categories' => false,
  'publish_posts'     => false,
  'edit_themes'       => false,
  'install_plugins'   => false,
  'update_plugin'     => false,
  'update_core'       => false
);
$usuario_generico = add_role('customer_b2b', __('Revendedor'), $usuario_generico_capabilities);*/


//check if role exist before removing it
if( get_role('customer_b2b') ){
      remove_role( 'customer_b2b' );
}


//==============================================
// Traduz o nome de usuário wholesale_customer para Atacado
//==============================================

function wps_change_role_name() {
	global $wp_roles;
	if ( ! isset( $wp_roles ) )
	$wp_roles = new WP_Roles();
	
	$wp_roles->roles['wholesale_customer']['name'] = 'Revendedor';
	$wp_roles->role_names['wholesale_customer'] = 'Revendedor';

}
add_action('init', 'wps_change_role_name');



//====================================================
// Campo customizado no Cadastro
//====================================================

/**
 * Add new register fields for WooCommerce registration.
 *
 * @return string Register fields HTML.
 */
function cs_wc_extra_register_fields() {
	?>
	<p class="form-row form-row-first">
		<label for="reg_billing_first_name"><?php _e( 'Nome', 'textdomain' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" required="required" />
	</p>
	<p class="form-row form-row-last">
		<label for="reg_billing_last_name"><?php _e( 'Sobrenome', 'textdomain' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>"  required="required" />
	</p>

	 <?php
	 //Se for Revendedor, Inclui os campos de Atacado
	
	if ( is_page('comprar-roupa-em-atacado') ) {
	?>
	<p class="form-row form-row-full">
		<label for="field_cpf_cnpj"><?php _e( 'CPF/CNPJ', 'textdomain' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="field_cpf_cnpj" id="reg_field_cpf_cnpj" value="<?php  if ( ! empty( $_POST['field_cpf_cnpj'] ) ) esc_attr_e( $_POST['field_cpf_cnpj'] ); ?>"  required="required" />
	</p>
	<p class="form-row form-row-first">
		<label for="field_celphone"><?php _e( 'Celular / Whatsapp', 'textdomain' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="field_celphone" id="reg_field_celphone" value="<?php  if ( ! empty( $_POST['field_celphone'] ) ) esc_attr_e( $_POST['field_celphone'] ); ?>"  required="required" />
	</p>
	<p class="form-row form-row-last">
		<label for="field_phone"><?php _e( 'Telefone', 'textdomain' ); ?></label>
		<input type="text" class="input-text" name="field_phone" id="reg_field_phone" value="<?php  if ( ! empty( $_POST['field_phone'] ) ) esc_attr_e( $_POST['field_phone'] ); ?>" />
	</p>

		<input type="hidden" name="role" id="reg_role" value="wholesale_customer" />
	</p>
	
	<?php } ?>

	<?php
}
add_action( 'woocommerce_register_form_start', 'cs_wc_extra_register_fields' );
/**
 * Validate the extra register fields.
 *
 * @param  string $username          Current username.
 * @param  string $email             Current email.
 * @param  object $validation_errors WP_Error object.
 *
 * @return void
 */
function cs_wc_validate_extra_register_fields( $username, $email, $validation_errors ) {
	if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
		$validation_errors->add( 'billing_first_name_error', __( '<strong>Erro</strong>: Digite o seu nome.', 'textdomain' ) );
	}
	if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
		$validation_errors->add( 'billing_last_name_error', __( '<strong>Erro</strong>: Digite o seu sobrenome.', 'textdomain' ) );
	}

	//Se for Revendedor, Inclui os campos de Atacado
	
	if ( isset( $_POST['role'] ) && 'wholesale_customer' == $_POST['role'] ) {

		if ( isset( $_POST['field_cpf_cnpj'] ) && empty( $_POST['field_cpf_cnpj'] ) ) {
			$validation_errors->add( 'field_cpf_cnpj_error', __( '<strong>Erro</strong>: Digite o seu CPF/CNPJ.', 'textdomain' ) );
		}

		if ( isset( $_POST['field_celphone'] ) && empty( $_POST['field_celphone'] ) ) {
			$validation_errors->add( 'field_celphone', __( '<strong>Erro</strong>: Digite o seu Celular de Whatsapp.', 'textdomain' ) );
		}

	}
}
add_action( 'woocommerce_register_post', 'cs_wc_validate_extra_register_fields', 10, 3 );
/**
 * Save the extra register fields.
 *
 * @param  int  $customer_id Current customer ID.
 *
 * @return void
 */
function cs_wc_save_extra_register_fields( $customer_id ) {
	if ( isset( $_POST['billing_first_name'] ) ) {
		// WordPress default first name field.
		update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
		// WooCommerce billing first name.
		update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
	}
	if ( isset( $_POST['billing_last_name'] ) ) {
		// WordPress default last name field.
		update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
		// WooCommerce billing last name.
		update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
	}


	 //Se for Revendedor, Inclui os campos de Atacado

	if ( isset( $_POST['role'] ) && 'wholesale_customer' == $_POST['role'] ) {

		 wp_update_user( array( 'ID' => $customer_id, 'role' => 'wholesale_customer' ) );

		if ( isset( $_POST['field_cpf_cnpj'] ) ) {
			update_user_meta( $customer_id, 'field_cpf_cnpj', sanitize_text_field( $_POST['field_cpf_cnpj'] ) );
		}

		if ( isset( $_POST['field_celphone'] ) ) {
			update_user_meta( $customer_id, 'field_celphone', sanitize_text_field( $_POST['field_celphone'] ) );
		}

		if ( isset( $_POST['field_phone'] ) ) {
			update_user_meta( $customer_id, 'field_phone', sanitize_text_field( $_POST['field_phone'] ) );
		}

		
	}
}
add_action( 'woocommerce_created_customer', 'cs_wc_save_extra_register_fields' );

//==================================================
// Exibiçãod as colunas na listagem de usuários
//==================================================
//add columns to User panel list page
function add_user_columns($column) {
    $column['field_celphone'] = 'Celular';
    $column['field_cpf_cnpj'] = 'CPF/CNPJ';

    return $column;
}
add_filter( 'manage_users_columns', 'add_user_columns' );

//add the data
function add_user_column_data( $val, $column_name, $user_id ) {
    $user = get_userdata($user_id);

    switch ($column_name) {
        case 'field_celphone' :
            return $user->field_celphone;
            break;
        case 'field_cpf_cnpj' :
            return $user->field_cpf_cnpj;
            break;
        
    }
    return;
}
add_filter( 'manage_users_custom_column', 'add_user_column_data', 10, 3 );

//===========================================================
//Remove Column Posts
//===========================================================
// Exclui a coluna POSTS da listagem de uruários

add_filter('manage_users_columns','remove_users_columns');
function remove_users_columns($column_headers) {
    
      unset($column_headers['posts']);
 
    return $column_headers;
}

//===============================
// Desloga o usuário, caso o seu cadastro ainda não esteja aprovado
//===============================

function ws_new_user_approve_autologout(){
       if ( is_user_logged_in() ) {
                $current_user = wp_get_current_user();
                $user_id = $current_user->ID;

                if ( get_user_meta($user_id, 'pw_user_status', true )  === 'approved' ){ $approved = true; }
		else{ $approved = false; }


		if ( $approved ){ 
			return $redirect_url;
		}
                else{ //when user not approved yet, log them out
			wp_logout();

                     /*   return add_query_arg( 'approved', 'false', get_permalink( get_option('woocommerce_myaccount_page_id') ) );*/
                }
        }
}

add_action('woocommerce_registration_redirect', 'ws_new_user_approve_autologout', 2);

// Customização nas Mensagens do Registro de Revendedor



function ws_new_user_approve_registration_message(){
if( is_page('comprar-roupa-em-atacado') ) {
        $not_approved_message = '<div class="Register--header u-displayFlex u-alignCenter u-flexAlignItemsCenter u-flexJustifyContentCenter u-paddingHorizontal--vrt--inter u-sizeFull u-marginHorizontal--inter u-alignCenter"><p class="registration resume">Ao completar o formulário de <strong>registro</strong> você deverá aguardar o prazo de até <strong>1 dia útil</strong> para que nossa equipe possa ativá-lo como <strong>revendedor(a)</strong>.</p></div>';

        if( isset($_REQUEST['approved']) ){
                $approved = $_REQUEST['approved'];
                if ($approved == 'false')  echo '<div class="Register--header u-displayFlex u-sizeFull u-marginHorizontal--inter u-alignCenter"><p class="registration successful resume">Cadastro concluído com Sucesso! Você será notificado por e-mail quando a sua conta de Revendedor for aprovada.</p></div>';
                else echo $not_approved_message;
        }
        else echo $not_approved_message;

        }
}
add_action('woocommerce_before_customer_login_form', 'ws_new_user_approve_registration_message', 2);



add_action('wp_logout','auto_redirect_after_logout');
	function auto_redirect_after_logout(){
	wp_redirect( get_home_url() . '/minha-conta/' );
exit();
}


//============================================================================
// HOOKS
//============================================================================


//=====================================================
// BLoco Atacado no SINGLE
//=====================================================

/**
 * Before Single Products Summary Div.
 *
 * @see woocommerce_single_product_summary()

 */

/*

add_action( 'woocommerce_single_product_summary', 'call_wholesale_in_product_single', 20 );


function call_wholesale_in_product_single( ) {

	if( !current_user_can('wholesale_customer') ) { 

	  $html = '<span class="u-marginBottom--inter u-displayBlock">(Preço no varejo)<br /></span><div class="woocommerce-loop-product__wholesaleBlock"><p class="WholesaleBlock-paragraph">Você quer ver o <strong>preço</strong> em <strong>Atacado?</strong></p><a class="Button Button--border Button--mediumSize u-borderRadius5 is-animating u-marginBottom--inter--half u-displayInlineBlock" href="' . get_permalink( get_page_by_path( 'comprar-roupa-em-atacado' ) ) . '">CADASTRE-SE</a></div>';

		print_r( $html );
	}
}*/

// Inativo por exemplo

/*
add_action( 'woocommerce_single_product_summary', 'call_wholesale_in_product_single', 20 );


function call_wholesale_in_product_single( ) {

	

	  $html = '<h3>EMBALAGEM GRÁTIS</h3>
Os acessórios Dogma acompanham uma embalagem exclusiva em MDF com gravação a laser. Uma ótima opção para presentear pessoas especiais, além de contribuir na conservação de sua peça.

<a class="http://api.whatsapp.com/send?1=pt_BR&amp;phone=5585981262984" href="http://api.whatsapp.com/send?1=pt_BR&amp;phone=5585981262984" target="_blank" rel="noopener noreferrer"><img class="alignnone wp-image-1413 " src="http://www.dogmastore.com.br/wp-content/uploads/2016/01/whatsapp-300x88.png" alt="" width="218" height="64" /></a>';

		print_r( $html );

}*/


/*<h3>EMBALAGEM GRÁTIS</h3>
Os acessórios Dogma acompanham uma embalagem exclusiva em MDF com gravação a laser. Uma ótima opção para presentear pessoas especiais, além de contribuir na conservação de sua peça.

<a href="http://www.dogmastore.com.br/wp-content/uploads/2017/09/banner-frete-gratis.png"><img class="alignnone wp-image-1399" src="http://www.dogmastore.com.br/wp-content/uploads/2017/09/banner-frete-gratis-300x111.png" alt="" width="181" height="67" /></a>  <a class="http://api.whatsapp.com/send?1=pt_BR&amp;phone=5585981262984" href="http://api.whatsapp.com/send?1=pt_BR&amp;phone=5585981262984" target="_blank" rel="noopener noreferrer"><img class="alignnone wp-image-1413 " src="http://www.dogmastore.com.br/wp-content/uploads/2016/01/whatsapp-300x88.png" alt="" width="218" height="64" /></a>*/



//=====================================================
// BLoco Atacado no LOOP
//=====================================================
/*
add_filter( 'woocommerce_loop_add_to_cart_link', 'call_wholesale_in_product_loop', 10, 2 );

function call_wholesale_in_product_loop( $html, $product ) {


		if ( $product && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {

			if( !current_user_can('wholesale_customer') ) { 

			$html = '<span class="u-marginBottom--inter">(Preço no varejo)<br /></span>' . $html . '<div class="woocommerce-loop-product__wholesaleBlock"><p class="WholesaleBlock-paragraph">Você quer ver o <strong>preço</strong> em <strong>Atacado?</strong></p><a class="Button Button--border Button--mediumSize u-borderRadius5 is-animating" href="' . get_permalink( get_page_by_path( 'comprar-roupa-em-atacado' ) ) . '">CADASTRE-SE</a></div>'; 
		} else {
			$html = $html;
		}

	return $html;
	}
}
*/

//==========================================================
// Minimo por compra
//==========================================================
if ( current_user_can('wholesale_customer') ) {

	$criterio = 'quantidade';

	if( $criterio == 'valor' ){

		// Set a minimum dollar amount per order
	add_action( 'woocommerce_check_cart_items', 'spyr_set_min_total' );
	function spyr_set_min_total() {
		// Only run in the Cart or Checkout pages
		if( is_cart() || is_checkout() ) {
			global $woocommerce;

			// Set minimum cart total
			$minimum_cart_total = 1000;

			// Total we are going to be using for the Math
			// This is before taxes and shipping charges
			$total = WC()->cart->subtotal;
			
			// Compare values and add an error is Cart's total
		    // happens to be less than the minimum required before checking out.
			// Will display a message along the lines of
			// A Minimum of 10 USD is required before checking out. (Cont. below)
			// Current cart total: 6 USD 
			if( $total <= $minimum_cart_total  ) {
				// Display our error message
				wc_add_notice( sprintf( '<strong>Um mínimo de R$ %s,00 <span class="u-isHidden">%s</span> é necessário para finalizar a compra.</strong>'
					.'<br />O valor somado até agora, totaliza R$ %s,00. <span class="u-isHidden">%s</span>',
					$minimum_cart_total,
					get_option( 'woocommerce_currency'),
					$total,
					get_option( 'woocommerce_currency') ),
				'error' );
			}
		}
	}

	} elseif ( $criterio == 'quantidade' ) {
		
		

		// Set a minimum number of products requirement before checking out
		add_action( 'woocommerce_check_cart_items', 'spyr_set_min_num_products' );
		function spyr_set_min_num_products() {
			// Only run in the Cart or Checkout pages
			if( is_cart() || is_checkout() ) {
				global $woocommerce;

				// Set the minimum number of products before checking out
				$minimum_num_products = 12;
				// Get the Cart's total number of products
				$cart_num_products = WC()->cart->cart_contents_count;

				// Compare values and add an error is Cart's total number of products
			    // happens to be less than the minimum required before checking out.
				// Will display a message along the lines of
				// A Minimum of 20 products is required before checking out. (Cont. below)
				// Current number of items in the cart: 6	
				if( $cart_num_products < $minimum_num_products ) {
					// Display our error message
			        wc_add_notice( sprintf( '<strong>Uma quantidade mínima de %s produtos é nescessária para finalizar a compra.</strong>' 
			        	. '<br />Atualmente, o seu carrinho possui apenas: %s.',
			        	$minimum_num_products,
			        	$cart_num_products ),
			        'error' );
				}
			}
		}
	}
}


//===========================================
//Cart Variations
//===========================================
function woocommerce_variable_add_to_cart(){
    global $product, $post;
 
    $variations = find_valid_variations();
 
    // Check if the special 'price_grid' meta is set, if it is, load the default template:
    if ( get_post_meta($post->ID, 'price_grid', true) ) {
        // Enqueue variation scripts
        wp_enqueue_script( 'wc-add-to-cart-variation' );
 
        // Load the template
        wc_get_template( 'single-product/add-to-cart/variable.php', array(
                'available_variations'  => $product->get_available_variations(),
                'attributes'            => $product->get_variation_attributes(),
                'selected_attributes'   => $product->get_variation_default_attributes()
            ) );
        return;
    }
 
    // Cool, lets do our own template!
    ?>
    <table class="variations_ variations-grid_" cellspacing="0">
        <tbody>

            <?php
            foreach ($variations as $key => $value) {
                if( !$value['variation_is_visible'] ) continue;
            ?>
            <tr class="u-displayFlex u-flexDirectionRow">
               
                    <?php foreach($value['attributes'] as $key => $val ) {
                        $val = str_replace(array('-','_'), ' ', $val);
                        printf( '<td class="u-displayFlex u-flexAlignItemsCenter attr_ _attr-%s">%s</td>', $key, ucwords($val) );
                    } ?>
                
                <td>
                    <?php echo $value['price_html'];?>
                </td>
                <td>
                    <?php if( $value['is_in_stock'] ) { ?>
                    <form class="cart_ u-displayFlex u-flexDirectionRow" action="<?php echo esc_url( $product->add_to_cart_url() ); ?>" method="post" enctype='multipart/form-data'>
                        <?php woocommerce_quantity_input(); ?>
                        <?php
                        if(!empty($value['attributes'])){
                            foreach ($value['attributes'] as $attr_key => $attr_value) {
                            ?>
                            <input type="hidden" name="<?php echo $attr_key?>" value="<?php echo $attr_value?>">
                            <?php
                            }
                        }
                        ?>
                        <button type="submit" class="single_add_to_cart_button_ btn_ btn-primary Button Button--background is-animating u-borderRadius5"><span class="glyphicon glyphicon-tag_"></span> ADICIONAR</button>
                        <input type="hidden" name="variation_id" value="<?php echo $value['variation_id']?>" />
                        <input type="hidden" name="product_id" value="<?php echo esc_attr( $post->ID ); ?>" />
                        <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $post->ID ); ?>" />
                    </form>
                    <?php } else { ?>
                        <p class="stock_ out-of-stock_"><?php _e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php
}


function find_valid_variations() {
    global $product;
 
    $variations = $product->get_available_variations();
    $attributes = $product->get_attributes();
    $new_variants = array();
 
    // Loop through all variations
    foreach( $variations as $variation ) {
 
        // Peruse the attributes.
 
        // 1. If both are explicitly set, this is a valid variation
        // 2. If one is not set, that means any, and we must 'create' the rest.
 
        $valid = true; // so far
        foreach( $attributes as $slug => $args ) {
            if( array_key_exists("attribute_$slug", $variation['attributes']) && !empty($variation['attributes']["attribute_$slug"]) ) {
                // Exists
 
            } else {
                // Not exists, create
                $valid = false; // it contains 'anys'
                // loop through all options for the 'ANY' attribute, and add each
                foreach( explode( '|', $attributes[$slug]['value']) as $attribute ) {
                    $attribute = trim( $attribute );
                    $new_variant = $variation;
                    $new_variant['attributes']["attribute_$slug"] = $attribute;
                    $new_variants[] = $new_variant;
                }
 
            }
        }
 
        // This contains ALL set attributes, and is itself a 'valid' variation.
        if( $valid )
            $new_variants[] = $variation;
 
    }
 
    return $new_variants;
}

//================================
//FOOTER
//================================



if ( ! function_exists( 'storefront_footer_widgets' ) ) {
	/**
	 * Display the footer widget regions.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_footer_widgets() {
		$rows    = intval( apply_filters( 'storefront_footer_widget_rows', 1 ) );
		$regions = intval( apply_filters( 'storefront_footer_widget_columns', 4 ) );

		for ( $row = 1; $row <= $rows; $row++ ) :

			// Defines the number of active columns in this footer row.
			for ( $region = $regions; 0 < $region; $region-- ) {
				if ( is_active_sidebar( 'footer-' . strval( $region + $regions * ( $row - 1 ) ) ) ) {
					$columns = $region;
					break;
				}
			}

			if ( isset( $columns ) ) : ?>
				<div class=<?php echo '"footer-widgets row-' . strval( $row ) . ' col-' . strval( $columns ) . ' fix u-paddingVertical"'; ?>><?php

					for ( $column = 1; $column <= $columns; $column++ ) :
						$footer_n = $column + $regions * ( $row - 1 );

						if ( is_active_sidebar( 'footer-' . strval( $footer_n ) ) ) : ?>

							<div class="block footer-widget-<?php echo strval( $column ); ?>">
								<?php dynamic_sidebar( 'footer-' . strval( $footer_n ) ); ?>
							</div><?php

						endif;
					endfor; ?>

				</div><!-- .footer-widgets.row-<?php echo strval( $row ); ?> --><?php

				unset( $columns );
			endif;
		endfor;
	}
}

if ( ! function_exists( 'storefront_credit' ) ) {
	/**
	 * Display the theme credit
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_credit() {
		?>
		<div class="site-info">
			<div class="u-paddingVertical u-maxSize--container u-alignCenterBox u-displayFlex u-flexDirectionRow">
				<div class="u-size12of24 u-marginRight">
					<?php echo esc_html( apply_filters( 'storefront_copyright_text', $content = '&copy; ' . get_bloginfo( 'name' ) . ' ' . date( 'Y' ) ) ); ?>
					<?php if ( apply_filters( 'storefront_credit_link', true ) ) { ?>
					<br />Todos os direitos Resevados.
					<?php } ?>
				</div>
				<div class="u-size11of24 u-alignRight">
					<a class="u-displayInlineBlock" href="http://www.i9me.com.br" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo-i9me-icon-site.png" /></a>
				</div>

			</div>
		</div><!-- .site-info -->
		<?php
	}
}