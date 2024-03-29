<?php
/**
 * General Functions
 *
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


/**
 * Get Theme Options
 */
if( ! function_exists( 'tie_get_option' )){

	function tie_get_option( $name, $default = false ){
		$get_options = get_option( apply_filters( 'TieLabs/theme_options', '' ) );

		if( ! empty( $get_options[ $name ] )){
			return $get_options[ $name ];
		}

		if ( $default ){
			return $default;
		}

		return false;
	}
}


/**
 * Get Post custom option
 */
if( ! function_exists( 'tie_get_postdata' )){

	function tie_get_postdata( $key, $default = false, $post_id = null ){

		if( ! $post_id ){
			$post_id = get_the_ID();
		}

		if( $value = get_post_meta( $post_id, $key, $single = true )){
			return $value;
		}
		elseif( $default ){
			return $default;
		}

		return false;
	}
}


/**
 * Get Category custom option
 */
if( ! function_exists( 'tie_get_category_option' )){

	function tie_get_category_option( $key, $category_id = 0 ){

		if( is_category() && empty( $category_id )){
			$category_id = get_query_var('cat');
		}

		if( empty( $category_id )){
			return false;
		}

		$categories_options = get_option( 'tie_cats_options' );

		if( ! empty( $categories_options[ $category_id ][ $key ] )){
			return $categories_options[ $category_id ][ $key ];
		}

		return false;
	}
}


/**
 * Get custom option > post > primary category > theme options
 */
if( ! function_exists( 'tie_get_object_option' )){

	function tie_get_object_option( $key = false, $cat_key = false, $post_key = false ){

		// CHeck if the $cat_key or $post_key are empty
		if( ! empty( $key ) ){
			$cat_key  = ! empty( $cat_key  ) ? $cat_key  : $key;
			$post_key = ! empty( $post_key ) ? $post_key : $key;
		}

		// Get Category options
		if( is_category() ){
			$option = tie_get_category_option( $cat_key );
		}

		// BuddyPress
		elseif( TIELABS_BUDDYPRESS_IS_ACTIVE && is_buddypress() ){

			$option = TIELABS_BUDDYPRESS::get_page_data( $post_key );
			$option = ( $option == 'default') ? '' : $option; //Compatability Sahifa
		}

		// WooCommerce
		elseif( TIELABS_WOOCOMMERCE_IS_ACTIVE && is_woocommerce() ){

			$option = TIELABS_WOOCOMMERCE::get_page_data( $post_key );
			$option = ( $option == 'default') ? '' : $option; //Compatability Sahifa
		}

		// Get Single options
		elseif( is_singular() ){

			// Get the post option if exists
			$option = tie_get_postdata( $post_key );

			$option = ( $option == 'default') ? '' : $option; //Compatability Sahifa

			// Get the category option if the post option isn't exists
			if( ( empty( $option ) || ( is_array( $option ) && ! array_filter( $option )) ) && is_singular( 'post' ) ){

				$category_id = tie_get_primary_category_id();
				$option      = tie_get_category_option( $cat_key, $category_id );
			}
		}

		// Get the global value
		if( ( empty( $option ) || ( is_array( $option ) && ! array_filter( $option )) ) && ! empty( $key ) ){
			$option = tie_get_option( $key );
		}

		if( ! empty( $option )){
			return $option;
		}

		return false;
	}
}


/**
 * Logo args
 */
if( ! function_exists( 'tie_logo_args' )){

	function tie_logo_args( $type = false ){

		$logo_args   = array();
		$logo_suffix = ( $type == 'sticky' ) ? '_sticky' : '';

		// Custom Post || Page logo
		if( is_singular() ){

			if( tie_get_postdata( 'custom_logo'.$logo_suffix )){

				$logo_args['logo_type']          = tie_get_postdata( 'logo_setting'.$logo_suffix );
				$logo_args['logo_img']           = tie_get_postdata( 'logo'.$logo_suffix );
				$logo_args['logo_retina']        = tie_get_postdata( 'logo_retina'.$logo_suffix );
				$logo_args['logo_width']         = tie_get_postdata( 'logo_retina_width'.$logo_suffix );
				$logo_args['logo_height']        = tie_get_postdata( 'logo_retina_height'.$logo_suffix );
				$logo_args['logo_margin_top']    = tie_get_postdata( 'logo_margin'.$logo_suffix );
				$logo_args['logo_margin_bottom'] = tie_get_postdata( 'logo_margin_bottom'.$logo_suffix );
				$logo_args['logo_title']         = tie_get_postdata( 'logo_text', get_bloginfo() );
				$logo_args['logo_url']           = tie_get_postdata( 'logo_url' );

			}
			// Get the category option if the post option isn't exists
			else{
				if( is_singular( 'post' ) ){
					$category_id = tie_get_primary_category_id();
				}
			}
		}

		// Custom category logo or primary category logo for a single post
		if( is_category() || ! empty( $category_id ) ){

			if( is_category() ){
				$category_id = get_query_var('cat');
			}

			if( tie_get_category_option( 'custom_logo'.$logo_suffix, $category_id )){

				$logo_args['logo_type']          = tie_get_category_option( 'logo_setting'.$logo_suffix,       $category_id );
				$logo_args['logo_img']           = tie_get_category_option( 'logo'.$logo_suffix,               $category_id );
				$logo_args['logo_retina']        = tie_get_category_option( 'logo_retina'.$logo_suffix,        $category_id );
				$logo_args['logo_width']         = tie_get_category_option( 'logo_retina_width'.$logo_suffix,  $category_id );
				$logo_args['logo_height']        = tie_get_category_option( 'logo_retina_height'.$logo_suffix, $category_id );
				$logo_args['logo_margin_top']    = tie_get_category_option( 'logo_margin'.$logo_suffix,        $category_id );
				$logo_args['logo_margin_bottom'] = tie_get_category_option( 'logo_margin_bottom'.$logo_suffix, $category_id );
				$logo_args['logo_title']         = tie_get_category_option( 'logo_text',                       $category_id ) ? tie_get_category_option( 'logo_text', $category_id ) : get_cat_name( $category_id );
				$logo_args['logo_url']           = tie_get_category_option( 'logo_url',                        $category_id );

			}
		}

		// Allow filtering the args
		$logo_args = apply_filters( 'TieLabs/Logo/args', $logo_args, $logo_suffix );


		// Get the theme default logo
		if( empty( $logo_args ) ){

			$logo_args['logo_type']          = tie_get_option( 'logo_setting'.$logo_suffix );
			$logo_args['logo_img']           = tie_get_option( 'logo'.$logo_suffix ) ? tie_get_option( 'logo'.$logo_suffix ) : get_theme_file_uri( '/assets/images/logo.png' );
			$logo_args['logo_width']         = tie_get_option( 'logo_retina_width'.$logo_suffix, 300 );
			$logo_args['logo_height']        = tie_get_option( 'logo_retina_height'.$logo_suffix, 49 );
			$logo_args['logo_margin_top']    = tie_get_option( 'logo_margin'.$logo_suffix );
			$logo_args['logo_margin_bottom'] = tie_get_option( 'logo_margin_bottom'.$logo_suffix );
			$logo_args['logo_title']         = tie_get_option( 'logo_text' ) ? tie_get_option( 'logo_text' ) : get_bloginfo();
			$logo_args['logo_url']           = tie_get_option( 'logo_url' );

			if( tie_get_option( 'logo_retina'.$logo_suffix ) ){
				$logo_args['logo_retina'] = tie_get_option( 'logo_retina'.$logo_suffix );
			}
			elseif( tie_get_option( 'logo'.$logo_suffix ) ){
				$logo_args['logo_retina'] = tie_get_option( 'logo'.$logo_suffix );
			}
			else{
				$logo_args['logo_retina'] = get_theme_file_uri( '/assets/images/logo@2x.png' );
			}
		}


		return $logo_args;
	}
}


/**
 * Sticky Logo args Function
 */
if( ! function_exists( 'tie_logo_sticky_args' )){

	function tie_logo_sticky_args(){

		// Sticky Logo is disabled
		if( ! tie_get_option( 'sticky_logo_type' ) ){
			return;
		}

		// Custom Site Sticky Logo
		if( tie_get_option( 'custom_logo_sticky' ) ){
			return tie_logo_args( 'sticky' );
		}

		// Site Logo
		return tie_logo_args();
	}
}


/**
 * Logo Function
 */
if( ! function_exists( 'tie_logo' )){

	function tie_logo(){

		$logo_args  = tie_logo_args();
		$logo_style = '';

		extract( $logo_args );

		// Logo URL
		$logo_url = ! empty( $logo_url ) ? $logo_url : home_url( '/' );

		// Logo Margin
		if( $logo_margin_top || $logo_margin_bottom ){

			$logo_style   = array();
			$logo_style[] = $logo_margin_top    ? "margin-top: {$logo_margin_top}px;"       : '';
			$logo_style[] = $logo_margin_bottom ? "margin-bottom: {$logo_margin_bottom}px;" : '';

			$logo_style = 'style="'. join( ' ', array_filter( $logo_style ) ) .'"';
		}

		// Logo Type : Title
		if( $logo_type == 'title' ){

			$logo_class  = 'text-logo';
			$logo_output = apply_filters( 'TieLabs/Logo/text_logo', '<div class="logo-text">'. $logo_title .'</div>', $logo_title );
		}

		// Logo Type : Image
		else{

			$logo_size 	= '';
			$logo_class	= 'image-logo';

			// Logo Width and Height
			if( $logo_width && $logo_height ){
				$logo_size = 'width="'. esc_attr( $logo_width ) .'" height="'. esc_attr( $logo_height ) .'" style="max-height:'. esc_attr( $logo_height ) .'px; width: auto;"';
			}

			// Logo Retina & Non Retina
			if( $logo_retina ){
				$logo_output = '
					<img src="'. esc_attr( $logo_img ) .'" alt="'. esc_attr( $logo_title ) .'" class="logo_normal" '. $logo_size .'>
					<img src="'. esc_attr( $logo_retina ) .'" alt="'. esc_attr( $logo_title ) .'" class="logo_2x" '. $logo_size .'>
				';
			}

			// Logo Non Retina
			else{
				$logo_output =
					'<img src="'. esc_attr( $logo_img ) .'" alt="'. esc_attr( $logo_title ) .'" '. $logo_size .'>';
			}
		}

		// H1 for the site title
		if( is_home() || is_front_page() ){
			$logo_output .= apply_filters( 'TieLabs/Logo/h1', '<h1 class="h1-off">'. $logo_title .'</h1>', $logo_title );
		}

		?>

		<div id="logo" class="<?php echo esc_attr( $logo_class ) ?>" <?php echo ( $logo_style ) ?>>

			<?php do_action( 'TieLabs/Logo/before_link' ); ?>

			<a title="<?php echo $logo_title ?>" href="<?php echo esc_url( apply_filters( 'TieLabs/Logo/url', $logo_url ) ) ?>">
				<?php
					do_action( 'TieLabs/Logo/before_img_text' );
					echo $logo_output;
					do_action( 'TieLabs/Logo/after_img_text' );
				?>
			</a>

			<?php do_action( 'TieLabs/Logo/after_link' ); ?>

		</div><!-- #logo /-->

		<?php
	}
}


/**
 * Sticky Logo Function
 */
if( ! function_exists( 'tie_sticky_logo' )){

	function tie_sticky_logo(){

		// Get the Sticky logo args
		$logo_args = tie_logo_sticky_args();

		if( ! $logo_args ){
			return;
		}

		extract( $logo_args );

		// Logo URL
		$logo_url = ! empty( $logo_url ) ? $logo_url : home_url( '/' );

		// Logo Type : Title
		if( $logo_type == 'title' ){

			// return if the type is text not image
			return;

			/*
				$logo_class  = 'text-logo';
				$logo_output = apply_filters( 'TieLabs/Logo/Sticky/text_logo', '<div class="logo-text">'. $logo_title .'</div>', $logo_title );
			*/
		}

		// Logo Type : Image
		else{

			$logo_size 	= '';
			$logo_class	= 'image-logo';

			// Logo Width and Height
			if( $logo_height && $logo_height < 50 ){
				$logo_size = 'style="max-height:'. esc_attr( $logo_height ) .'px; width: auto;"';
			}

			// Logo Retina & Non Retina
			if( $logo_retina ){
				$logo_output = '
					<img src="'. esc_attr( $logo_img ) .'" alt="'. esc_attr( $logo_title ) .'" class="logo_normal" '. $logo_size .'>
					<img src="'. esc_attr( $logo_retina ) .'" alt="'. esc_attr( $logo_title ) .'" class="logo_2x" '. $logo_size .'>
				';
			}
			// Logo Non Retina
			else{
				$logo_output =
					'<img src="'. esc_attr( $logo_img ) .'" alt="'. esc_attr( $logo_title ) .'" '. $logo_size .'>';
			}
		}

		?>

		<div id="sticky-logo" class="<?php echo esc_attr( $logo_class ) ?>">

			<?php do_action( 'TieLabs/Logo/Sticky/before_link' ); ?>

			<a title="<?php echo $logo_title ?>" href="<?php echo esc_url( apply_filters( 'TieLabs/Logo/Sticky/url', $logo_url ) ) ?>">
				<?php
					do_action( 'TieLabs/Logo/Sticky/before_img_text' );
					echo $logo_output;
					do_action( 'TieLabs/Logo/Sticky/after_img_text' );
				?>
			</a>

			<?php do_action( 'TieLabs/Logo/Sticky/after_link' ); ?>

		</div><!-- #Sticky-logo /-->

		<div class="flex-placeholder"></div>

		<?php
	}
}


/*
 * Custom Quries
 */
if( ! function_exists( 'tie_query' )){

	function tie_query( $block = array() ){

		$args = array(
			'post_status'         => array( 'publish' ),
			'posts_per_page'      => 5,
			'ignore_sticky_posts' => true,
		);

		// Posts Status for the Ajax Requests
		if( is_user_logged_in() && current_user_can('read_private_posts') ){
			$args['post_status'] = array( 'publish', 'private' );
		}

		// Posts Number
		if( ! empty( $block['number'] )){
			$args['posts_per_page'] = $block['number'];
		}


		// Tags : Post Query
		if( ! empty( $block['tags'] )){

			$tags = array_unique( explode( ',', $block['tags'] ) );

			$args['tag__in'] = array();

			foreach ( $tags as $tag ){
				$post_tag =TIELABS_WP_HELPER::get_term_by( 'name', trim( $tag ), 'post_tag' );

				if( ! empty( $post_tag )){
					$args['tag__in'][] = $post_tag->term_id;
				}
			}
		}

		// Tags_array : Post Query
		elseif( ! empty( $block['tags_ids'] )){

			$args['tag__in'] = $block['tags_ids'];
		}

		// Posts : Post Query - Used by the JetPack quries
		elseif( ! empty( $block['posts'] )){

			$selective_posts  = explode ( ',', $block['posts'] );
			$args['orderby']  = 'post__in';
			$args['post__in'] = $selective_posts;

			// Use the count Added posts as the number of posts value
			if( ! empty( $block['use_posts_count'] ) ){

				$selective_posts_number	= count( $selective_posts );
				$args['posts_per_page']	= $selective_posts_number;
			}
		}

		// Pages : Post Query
		elseif( ! empty( $block['pages'] )){

			$selective_pages        = explode ( ',', $block['pages'] );
			$selective_pages_number = count( $selective_pages );
			$args['orderby']        = 'post__in';
			$args['post__in']       = $selective_pages;
			$args['posts_per_page']	= $selective_pages_number;
			$args['post_type']      = 'page';
		}

		// Author : Post Query
		elseif( ! empty( $block['author'] )){

			$args['author'] = $block['author'];
		}

		// Categories : Post Query
		else{

			if( ! empty( $block['id'] ) ){
				$block_cat = maybe_unserialize( $block['id'] );
				$args['cat'] = is_array( $block_cat ) ? implode( ',', $block_cat ) : $block_cat;
			}
		}

		// Exclude Posts
		if( ! empty( $block['exclude_posts'] ) ){
			$args['post__not_in'] = explode ( ',', $block['exclude_posts'] );
		}

		// Posts Order
		if( ! empty( $block['order'] ) ){

			// Random Posts
			if( $block['order'] == 'rand' ){
				$args['orderby'] = 'rand';
			}

			// Most Viewd posts
			elseif( $block['order'] == 'views' && tie_get_option( 'tie_post_views' ) ){
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = apply_filters( 'TieLabs/views_meta_field', 'tie_views' );
			}

			// Popular Posts by comments
			elseif( $block['order'] == 'popular' ){
				$args['orderby'] = 'comment_count';
			}

			// Recent modified Posts
			elseif( $block['order'] == 'modified' ){
				$args['orderby'] = 'modified';
			}
		}

		// Pagination
		if ( ! empty( $block['pagi'] ) ){

			$paged = 1;

			if( ! empty( $block['target_page'] ) ){
				$paged = intval( $block['target_page'] );
			}

			elseif( $block['pagi'] == 'numeric' ){
				$paged   = intval( get_query_var( 'paged' ));
				$paged_2 = intval( get_query_var( 'page'  ));

				if( empty( $paged ) && ! empty( $paged_2 )  ){
					$paged = intval( get_query_var('page') );
				}
			}

			$args['paged'] = $paged;
		}
		else{
			$args['no_found_rows'] = true ;
		}

		// Offset
		if( ! empty( $block['offset'] ) ){

			if( ! empty( $block['pagi'] ) && ! empty( $paged ) ){
				$args['offset'] = $block['offset'] + ( ($paged-1) * $args['posts_per_page'] );
			}

			else{
				$args['offset'] = $block['offset'];
			}
		}

		// Do not duplicate posts
		if( ! empty( $GLOBALS['tie_do_not_duplicate_builder'] ) && is_array( $GLOBALS['tie_do_not_duplicate_builder'] )){
			$args['post__not_in'] = $GLOBALS['tie_do_not_duplicate_builder'];
		}

		// Allow making changes on the Query
		$args = apply_filters( 'TieLabs/Query/args', $args, $block );

		// Run the Query
		$block_query = tie_run_the_query( $args );

		// Fix the numbe of pages WordPress Offset bug with pagination
		if(	! empty( $block['pagi'] )){

			if( ! empty( $block['offset'] )){

				// Modify the found_posts
				$found_posts = $block_query->found_posts;
				$found_posts = $found_posts - $block['offset'];
				$block_query->set( 'new_found_posts', $found_posts );

				// Modify the max_num_pages
				$block_query->set( 'new_max_num_pages', ceil( $found_posts/$args['posts_per_page'] ) );
			}
			else{
				$block_query->set( 'new_max_num_pages', $block_query->max_num_pages );
			}
		}

		return $block_query;
	}
}


/*
 * Run the Quries and Cache them
 */
function tie_run_the_query( $args = array() ){

	// Check if the theme cache is enabled
	if ( ! tie_get_option( 'cache' )){
		return new WP_Query( $args );
	}

	// Prepare the cache key
	$cache_key = http_build_query( $args );

  // Check for the custom key in the theme group
  $custom_query = wp_cache_get( $cache_key, 'tie_theme' );

  // If nothing is found, build the object.
  if ( false === $custom_query ) {
    $custom_query = new WP_Query( $args );

    if ( ! is_wp_error( $custom_query ) && $custom_query->have_posts() ) {
			wp_cache_set( $cache_key, $custom_query, 'tie_theme' );
    }
  }

  return $custom_query;
}


/**
 * Block title
 */
if( ! function_exists( 'tie_block_title' )){

	function tie_block_title( $block = false ){

		if( empty( $block['title'] )) return; ?>

		<div <?php tie_box_class( 'mag-box-title' ) ?>>
			<h3>
				<?php

					if( ! empty( $block['url'] )){
						echo '<a href="'. esc_url( $block['url'] ) .'" title="'.$block['title'].'">';
						echo $block['title'];
						echo '</a>';
					}
					else{
						echo $block['title'];
					}
				?>
			</h3>

			<?php
			$block_options = '';

			// Block filter
			if( ! empty( $block['filters'] ) && $block['pagi'] != 'numeric' ){

				$block_options .= '
				<ul class="mag-box-filter-links">
					<li><a href="#" class="block-ajax-term active" >'. esc_html__( 'All', TIELABS_TEXTDOMAIN ) .'</a></li>';

					// Filter by tags
					if( ! empty( $block['tags'] )){

						$tags = TIELABS_HELPER::remove_spaces( $block['tags'] );
						$tags = array_unique( explode( ',', $tags ) );

						foreach ( $tags as $tag ){
							$post_tag =TIELABS_WP_HELPER::get_term_by( 'name', $tag, 'post_tag' );

							if( ! empty( $post_tag ) && ! empty( $post_tag->count ) && ( $block['offset'] < $post_tag->count )){
								$block_options .= '<li><a href="#" data-id="'.$post_tag->name.'" class="block-ajax-term" >'. $post_tag->name .'</a></li>';
							}
						}
					}

					// Filter by categories
					elseif( ! empty( $block['id'] ) && is_array( $block['id'] )){
						foreach ( $block['id'] as $cat_id ){
							$get_category = TIELABS_WP_HELPER::get_term_by( 'id', $cat_id, 'category');

							if( ! empty( $get_category ) && ! empty( $get_category->count ) && ( $block['offset'] < $get_category->count )){
								$block_options .= '<li><a href="#" data-id="'.$cat_id.'" class="block-ajax-term" >'. $get_category->name .'</a></li>';
							}
						}
					}
				$block_options .= '</ul>';
			}

			// More Button
			if( ! empty( $block['more'] ) && ! empty( $block['url'] ) ){
				$block_options .= '<a class="block-more-button" href="'. esc_url( $block['url'] ) .'">'. esc_html__( 'More', TIELABS_TEXTDOMAIN ) .'</a>';
			}

			// Ajax Block Arrows
			if( ! empty( $block['pagi'] ) && $block['pagi'] == 'next-prev' ){
				$block_options .= '
					<ul class="slider-arrow-nav">
						<li><a class="block-pagination prev-posts pagination-disabled" href="#"><span class="fa fa-angle-left" aria-hidden="true"></span></a></li>
						<li><a class="block-pagination next-posts" href="#"><span class="fa fa-angle-right" aria-hidden="true"></span></a></li>
					</ul>
				';
			}

			// Scrolling Block Arrows
			if( ! empty( $block['scrolling_box'] )){
				$block_options .= '<ul class="slider-arrow-nav"></ul>';
			}


			if( ! empty( $block_options ) ){
				echo '
					<div class="tie-alignright">
						<div class="mag-box-options">
							'. $block_options .'
						</div><!-- .mag-box-options /-->
					</div><!-- .tie-alignright /-->
				';
			}

		echo '</div><!-- .mag-box-title /-->';
	}
}


/**
 * Author Box
 */
if( ! function_exists( 'tie_author_box' )){

	function tie_author_box( $name = false, $user_id = false ){

		if( empty( $user_id ) ){
			$user_id = get_query_var( 'author' );
		}

		$description = get_the_author_meta( 'description', $user_id );

		/* Don't show the box if the user doesn't have a bio description---
			if( empty( $description ) ){
				return;
			}
		*/

		?>

		<div class="about-author container-wrapper">

			<?php

				// Show the avatar if it is active only
				if( get_option( 'show_avatars' ) ){ ?>
					<div class="author-avatar">
						<a href="<?php echo tie_get_author_profile_url( $user_id ); ?>">
							<?php echo get_avatar( get_the_author_meta( 'user_email', $user_id ), apply_filters( 'TieLabs/Author_Box/avatar_size', 180 ) ); ?>
						</a>
					</div><!-- .author-avatar /-->
					<?php
				}

			?>

			<div class="author-info">
				<h3 class="author-name"><a href="<?php echo tie_get_author_profile_url( $user_id ); ?>"><?php echo esc_html( $name ) ?></a></h3>

				<div class="author-bio">
					<?php echo wp_kses_post( $description ); ?>
				</div><!-- .author-bio /-->

				<?php

					// Add the website URL
					$author_social = tie_author_social_array();
					$website = array(
						'url' => array(
							'text' => esc_html__( 'Website', TIELABS_TEXTDOMAIN ),
							'icon' => 'home',
						));

					$author_social = array_merge( $website, $author_social );

					// Generate the social icons
					echo '<ul class="social-icons">';

					foreach ( $author_social as $network => $button ){
						if( get_the_author_meta( $network , $user_id )){
							$icon = empty( $button['icon'] ) ? $network : $button['icon'];

							echo '
								<li class="social-icons-item">
									<a href="'. esc_url( get_the_author_meta( $network, $user_id ) ) .'" rel="external" target="_blank" class="social-link '. $network .'-social-icon">
									<span class="fa fa-'. $icon .'" aria-hidden="true"></span>
									<span class="screen-reader-text">'. $button['text'] .'</span>
									</a>
								</li>
							';
						}
					}

					echo '</ul>';
				?>
			</div><!-- .author-info /-->
			<div class="clearfix"></div>
		</div><!-- .about-author /-->
		<?php
	}
}


/**
 * Get posts in a Widget
*/
if( ! function_exists( 'tie_widget_posts' )){

	function tie_widget_posts( $query_args = array(), $args = array() ){

		$args = wp_parse_args( $args, array(
			'thumbnail'       => TIELABS_THEME_SLUG.'-image-small',
			'thumbnail_first' => '',
			'review'          => 'small',
			'review_first'    => '',
			'count'           => 0,
			'show_score'      => true,
			'title_length'    => '',
			'exclude_current' => false,
		));

		$query_args = apply_filters( 'TieLabs/posts_widget_query', $query_args );

		// Exclude the Current Post
		if( $args['exclude_current'] && is_single() ){
			$query_args['exclude_posts'] = get_the_id();
		}

		// Related Posts Order
		if( ! empty( $query_args['order'] ) && strpos( $query_args['order'], 'related' ) !== false  ){

			$related_type = $query_args['order'];

			// Exclude the Current Post from the related posts
			$query_args['exclude_posts'] = get_the_id();

			// Unset the attrs
			unset( $query_args['id'] );
			unset( $query_args['order'] );

			// Related By Author
			if( $related_type == 'related-author' ){
				$query_args['author'] = get_the_author_meta( 'ID' );
			}

			// Related By Tags
			elseif( $related_type == 'related-tag' ){

				$post_tags = get_the_terms( get_the_id(), 'post_tag' );

				if( ! empty( $post_tags ) ){
					foreach( $post_tags as $individual_tag ){
						$tags_ids[] = $individual_tag->term_id;
					}

					$query_args['tags_ids'] = $tags_ids;
				}
			}

			// Related by Cats
			elseif( $related_type == 'related-cat' ){

				$category_ids = array();
				$categories   = get_the_category( get_the_id() );

				foreach( $categories as $individual_category ){
					$category_ids[] = $individual_category->term_id;
				}

				$query_args['id'] = $category_ids;
			}
		}

		// Run the query
		$query = tie_query( $query_args );

		if ( $query->have_posts() ){
			while ( $query->have_posts() ){ $query->the_post();

				$args['count']++;

				if( ! empty( $args['style'] ) && $args['style'] == 'timeline' ){ ?>
					<li>
						<a href="<?php the_permalink(); ?>">
							<?php tie_get_time() ?>
							<h3><?php the_title();?></h3>
						</a>
					</li>
					<?php
				}

				elseif( ! empty( $args['style'] ) && $args['style'] == 'grid' ){
					if ( has_post_thumbnail() ){ ?>
						<div <?php tie_post_class( 'tie-col-xs-4' ); ?>>
							<?php tie_post_thumbnail( TIELABS_THEME_SLUG.'-image-large', false ); ?>
						</div>
						<?php
					}
				}

				elseif( ! empty( $args['style'] ) && $args['style'] == 'authors' ){
					TIELABS_HELPER::get_template_part( 'templates/loops/loop', 'authors-widget', $args );
				}

				else{
					TIELABS_HELPER::get_template_part( 'templates/loops/loop', 'widgets', $args );
				}
			}
		}

		wp_reset_postdata();
	}
}


/**
 * Get recent comments
 */
if( ! function_exists( 'tie_recent_comments' )){

	function tie_recent_comments( $comment_posts = 5, $avatar_size = 70 ){

		$comments = get_comments( 'status=approve&number='.$comment_posts );

		foreach ($comments as $comment){ ?>
			<li>
				<?php

				$no_thumb = 'no-small-thumbs';

				// Show the avatar if it is active only
				if( get_option( 'show_avatars' ) ){

					$no_thumb = ''; ?>
					<div class="post-widget-thumbnail" style="width:<?php echo esc_attr( $avatar_size ) ?>px">
						<a class="author-avatar" href="<?php echo get_permalink($comment->comment_post_ID ); ?>#comment-<?php echo esc_attr( $comment->comment_ID ); ?>">
							<?php echo get_avatar( $comment, $avatar_size ); ?>
						</a>
					</div>
					<?php
				}

				?>

				<div class="comment-body <?php echo esc_attr( $no_thumb ) ?>">
					<a class="comment-author" href="<?php echo get_permalink($comment->comment_post_ID ); ?>#comment-<?php echo esc_attr( $comment->comment_ID ); ?>">
						<?php echo strip_tags($comment->comment_author); ?>
					</a>
					<p><?php echo wp_html_excerpt( $comment->comment_content, 60 ); ?>...</p>
				</div>

			</li>
			<?php
		}
	}
}


/**
 * Login Form
 */
if( ! function_exists( 'tie_login_form' )){

	function tie_login_form( $login_only = false ){

		$redirect     = apply_filters( 'TieLabs/Login/redirect', site_url() );
		$current_user = wp_get_current_user();

		if ( is_user_logged_in() && empty( $login_only ) ){

			// Build the Array of custom links
			$logged_in_links = array();

			// Add the Dashboared Link
			if( current_user_can( 'switch_themes' ) ){
				$logged_in_links['dashboard'] = array(
					'icon' => 'fa fa-cog',
					'link' => admin_url(),
					'text' => esc_html__( 'Dashboard', TIELABS_TEXTDOMAIN ),
				);
			}

			// Profile Page
			$logged_in_links['profile'] = array(
				'icon' => 'fa fa-user',
				'link' => get_edit_profile_url(),
				'text' => esc_html__( 'Your Profile', TIELABS_TEXTDOMAIN ),
			);

			// Profile Page
			$logged_in_links['logout'] = array(
				'icon' => 'fa fa-sign-out',
				'link' => wp_logout_url( $redirect ),
				'text' => esc_html__( 'Log Out', TIELABS_TEXTDOMAIN ),
			);

			$logged_in_links = apply_filters( 'TieLabs/Login/links', $logged_in_links );

			?>

			<div class="is-logged-login">

				<?php

					do_action( 'TieLabs/Login/before_avatar' );

					// Show the avatar if it is active only
					if( get_option( 'show_avatars' ) ){ ?>
						<span class="author-avatar">
							<a href="<?php echo tie_get_author_profile_url( $current_user->ID ) ?>"><?php echo get_avatar( $current_user->ID, apply_filters( 'TieLabs/Login/avatar_size', '90' ) ); ?></a>
						</span>
						<?php
					}

					do_action( 'TieLabs/Login/after_avatar' );
				?>

				<h4 class="welcome-text">
					<?php esc_html_e( 'Welcome', TIELABS_TEXTDOMAIN ) ?> <strong><?php echo esc_html( $current_user->display_name ) ?></strong>
				</h4>

				<?php

					do_action( 'TieLabs/Login/before_links' );

					if( ! empty( $logged_in_links ) && is_array( $logged_in_links ) ){

						echo '<ul class="user-logged-links">';

						foreach ( $logged_in_links as $link_id => $link_data ){
							echo '<li><span class="'. $link_data['icon'] .'" aria-hidden="true"></span> <a href="'. esc_url( $link_data['link'] ) .'">'. $link_data['text'] .'</a></li>';
						}

						echo '</ul>';
					}


					do_action( 'TieLabs/Login/after_links' );
				?>

			</div>

			<?php
		}

		else{

			do_action( 'TieLabs/Login/before_form' ); ?>

			<div class="login-form">

				<form name="registerform" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ) ?>" method="post">
					<input type="text" name="log" title="<?php esc_html_e( 'Username', TIELABS_TEXTDOMAIN ) ?>" placeholder="<?php esc_html_e( 'Username', TIELABS_TEXTDOMAIN ) ?>">
					<div class="pass-container">
						<input type="password" name="pwd" title="<?php esc_html_e( 'Password', TIELABS_TEXTDOMAIN ) ?>" placeholder="<?php esc_html_e( 'Password' ) ?>">
						<a class="forget-text" href="<?php echo wp_lostpassword_url( $redirect ) ?>"><?php esc_html_e( 'Forget?', TIELABS_TEXTDOMAIN ) ?></a>
					</div>

					<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $_SERVER['REQUEST_URI'] ); ?>"/>
					<label for="rememberme" class="rememberme">
						<input id="rememberme" name="rememberme" type="checkbox" checked="checked" value="forever" /> <?php esc_html_e( 'Remember me', TIELABS_TEXTDOMAIN ) ?>
					</label>

					<?php do_action( 'TieLabs/Login/before_button' ); ?>

					<?php echo apply_filters( 'gglcptch_display_recaptcha', '' ); ?>

					<button type="submit" class="button fullwidth login-submit"><?php esc_html_e( 'Log In', TIELABS_TEXTDOMAIN ) ?></button>

					<?php do_action( 'TieLabs/Login/after_button' ); ?>
				</form>

				<?php

					do_action( 'TieLabs/Login/after_form' );

					// Compatibility with the WordPress Social Login plugin
					do_action( 'wordpress_social_login' );

					// Register Link
					if ( get_option( 'users_can_register' ) ){

						$registration_url = sprintf( '<p class="register-link"><a href="%1$s">%2$s</a></p>', esc_url( wp_registration_url() ), esc_html__( "Don't have an account?", TIELABS_TEXTDOMAIN ) );
						echo apply_filters( 'register', $registration_url );
					}
				?>

			</div>
			<?php
		}
	}
}



/**
 * Rich Snippets
 */
if( ! function_exists( 'tie_article_schemas' )){

	add_action( 'TieLabs/after_post_entry',  'tie_article_schemas' );
	function tie_article_schemas(){

		if( ! tie_get_option( 'structure_data' ) ){
			return false;
		}

		$post    = get_post();
		$post_id = $post->ID;

		// Check if the rich snippts supported on pages?
		if( is_page() && ! apply_filters( 'TieLabs/is_page_rich_snippet', false ) ){
			return;
		}

		// Site Logo
		$site_logo = tie_get_option( 'logo_retina' ) ? tie_get_option( 'logo_retina' ) : tie_get_option( 'logo' );
		$site_logo = ! empty( $site_logo ) ? $site_logo : get_stylesheet_directory_uri().'/assets/images/logo@2x.png';

		// Post data
		$article_body   = strip_tags(strip_shortcodes( apply_filters( 'TieLabs/exclude_content', $post->post_content ) ));
		$description    = wp_html_excerpt( $article_body, 200 );
		$puplished_date = ( get_the_time( 'c' ) ) ? get_the_time( 'c' ) : get_the_modified_date( 'c' );
		$modified_date  = ( get_the_modified_date( 'c' ) ) ? get_the_modified_date( 'c' ) : $puplished_date;

		$schema_type    = tie_get_object_option( 'schema_type', 'cat_schema_type', false );
		$schema_type    = ! empty( $schema_type ) ? $schema_type : 'Article';

		// The Scemas Array
		$schema = array(
			'@context'       => 'http://schema.org',
			'@type'          => $schema_type,
			'dateCreated'    => $puplished_date,
			'datePublished'  => $puplished_date,
			'dateModified'   => $modified_date,
			'headline'       => get_the_title(),
			'name'           => get_the_title(),
			'keywords'       => tie_get_plain_terms( $post_id, 'post_tag' ),
			'url'            => get_permalink(),
			'description'    => $description,
			'copyrightYear'  => get_the_time( 'Y' ),
			'articleSection' => tie_get_plain_terms( $post_id, 'category' ),
			'articleBody'    => $article_body,
			'publisher'      => array(
				'@id'   => '#Publisher',
				'@type' => 'Organization',
				'name'  => get_bloginfo(),
				'logo'  => array(
						'@type'  => 'ImageObject',
						'url'    => $site_logo,
				)
			),
			'sourceOrganization' => array(
				'@id' => '#Publisher'
			),
			'copyrightHolder' => array(
				'@id' => '#Publisher'
			),
			'mainEntityOfPage' => array(
				'@type'      => 'WebPage',
				'@id'        => get_permalink(),
			),
			'author' => array(
				'@type' => 'Person',
				'name'  => get_the_author(),
				'url'   => tie_get_author_profile_url(),
			),
		);

		// Post image
		$image_id   = get_post_thumbnail_id();
		$image_data = wp_get_attachment_image_src( $image_id, 'full' );

		if( ! empty( $image_data ) ){
			$schema['image'] = array(
				'@type'  => 'ImageObject',
				'url'    => $image_data[0],
				'width'  => ( $image_data[1] > 696 ) ? $image_data[1] : 696,
				'height' => $image_data[2],
			);
		}

		// Breadcrumbs
		if( tie_get_option( 'breadcrumbs' ) ){
			$schema['mainEntityOfPage']['breadcrumb'] = array(
				'@id' => '#Breadcrumb'
			);
		}

		// Social links
		$social = tie_get_option( 'social' );
		if( ! empty( $social ) && is_array( $social )){
			$schema['publisher']['sameAs'] = array_values( $social );
		}

		//-
		$schema = apply_filters( 'TieLabs/rich_snippet_schema', $schema );

		// Print the schema
		if( $schema ){
			echo '<script type="application/ld+json">'. json_encode( $schema ) .'</script>';
		}

	}
}


/*
 * Get the Ajax loader icon
 */
if( ! function_exists( 'tie_get_ajax_loader' )){

	function tie_get_ajax_loader( $echo = true ){

		$out = '<div class="loader-overlay">';

		if( tie_get_option( 'loader-icon' ) == 2 ){
			$out .= '
				<div class="spinner">
					<div class="bounce1"></div>
					<div class="bounce2"></div>
					<div class="bounce3"> </div>
				</div>
			';
		}
		else{
			$out .= '<div class="spinner-circle"></div>';
		}

		$out .= '</div>';

		if( $echo ){
			echo ( $out );
		}

		return $out;
	}
}


/*
 * Get the author profile link
 */
if( ! function_exists( 'tie_get_author_profile_url' )){

	function tie_get_author_profile_url( $user_id = false ){

		if( empty( $user_id ) ){
			$user_id = get_the_author_meta( 'ID' );
		}

		// Use the BuddyPress member profile page
		if( TIELABS_BUDDYPRESS_IS_ACTIVE && tie_get_option( 'bp_use_member_profile' ) ){

			return bp_core_get_user_domain( $user_id );
		}

		// Use the default Author profile page
		return get_author_posts_url( $user_id );
	}
}



/*
 * Mobile Menu icon
 */
if( ! function_exists( 'tie_add_mobile_menu_trigger' )){

	add_action( 'TieLabs/Logo/before', 'tie_add_mobile_menu_trigger' );
	function tie_add_mobile_menu_trigger(){

		if( ! tie_get_option( 'mobile_menu_active' ) ) return;

		echo '<a href="#" id="mobile-menu-icon">';
		echo '<span class="nav-icon"></span>';

		if( tie_get_option( 'mobile_menu_text' ) ){
			echo '<span class="menu-text">'. esc_html__( 'Menu', TIELABS_TEXTDOMAIN ) .'</span>';
		}

		echo'</a>';
	}
}




/*
 * Social
 */
if( ! function_exists( 'tie_get_social' )){

	function tie_get_social( $options = array() ){

		/**
		 * $options{}
		 * 		- $tooltip
		 *		- $before
		 *		- $after
		 */

		$defaults = array(
			'tooltip'   => '',
			'reverse'   => false,
			'show_name' => false,
			'before' 		=> "<ul>",
			'after' 		=> "</ul> \n",
		);

		$options = wp_parse_args( $options, $defaults );
		extract( $options );

		$social 		  = tie_get_option( 'social' );
		$social_class = 'social-link '.$tooltip;

		// RSS
		if ( tie_get_option( 'rss_icon' ) ){
			$social['rss'] = ! empty( $social['rss'] ) ? $social['rss'] : get_bloginfo( 'rss2_url' );
		}

		$social_array = tie_social_networks();

		// Custom Social Networking
		for( $i=1 ; $i<=5 ; $i++ ){

			if ( tie_get_option( "custom_social_icon_$i" ) && tie_get_option( "custom_social_url_$i" ) && tie_get_option( "custom_social_title_$i" ) ){

				$custom_name = "custom-link-$i";
				$social[ $custom_name ] = tie_get_option( "custom_social_url_$i" );

				$social_array[ $custom_name ]	= array(
					'title'	=> tie_get_option( "custom_social_title_$i" ),
					'icon'	=> 'fa ' . tie_get_option( "custom_social_icon_$i" ),
					'class'	=> 'social-custom-link ' . $custom_name );
			}
		}

		if( $reverse && is_array( $social ) ){
			$social = array_reverse( $social );
		}

		// Print the Icons
		echo ( $before );

		if( ! empty($social) && is_array( $social ) ){
			foreach ( $social as $network => $link ){
				if( ! empty( $link ) && ! empty( $social_array[ $network ] ) ){
					$icon  = $social_array[ $network ]['icon'];
					$class = $social_array[ $network ]['class'] . '-social-icon';
					$title = $social_array[ $network ]['title'];
					$text  = '';

					if( ! empty( $show_name ) ){
						$text = '<span class="social-text">'.$title.'</span>';
					}

					echo'<li class="social-icons-item"><a class="'. $social_class .' '. $class .'" title="'. $title .'" rel="nofollow" target="_blank" href="'. esc_url( $link ) .'"><span class="'. $icon .'"></span>'. $text .'</a></li>';
				}
			}
		}

		echo ( $after );
	}
}


/*
 * Social Networks
 */
if( ! function_exists( 'tie_social_networks' )){

	function tie_social_networks(){

		$social_array = array(
			'rss'	=> array(
				'title'       => esc_html__( 'RSS', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-rss',
				'hint'  => esc_html__( 'Optional Custom Feed URL, Leave it empty to use the default WordPress feed URL.', TIELABS_TEXTDOMAIN ),
				'class' => 'rss',
			),

			'google_plus' => array(
				'title'       => esc_html__( 'Google+', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-google-plus',
				'class' => 'google',
			),

			'facebook' => array(
				'title'       => esc_html__( 'Facebook', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-facebook',
				'class' => 'facebook',
			),

			'twitter' => array(
				'title'       => esc_html__( 'Twitter', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-twitter',
				'class' => 'twitter',
			),

			'Pinterest' => array(
				'title'       => esc_html__( 'Pinterest', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-pinterest',
				'class' => 'pinterest',
			),

			'dribbble' => array(
				'title'       => esc_html__( 'Dribbble', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-dribbble',
				'class' => 'dribbble',
			),

			'linkedin' => array(
				'title'       => esc_html__( 'LinkedIn', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-linkedin',
				'class' => 'linkedin',
			),

			'flickr' => array(
				'title'       => esc_html__( 'Flickr', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-flickr',
				'class' => 'flickr',
			),

			'youtube' => array(
				'title'       => esc_html__( 'YouTube', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-youtube',
				'class' => 'youtube',
			),

			'digg' => array(
				'title'       => esc_html__( 'Digg', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-digg',
				'class' => 'digg',
			),

			'reddit' => array(
				'title'       => esc_html__( 'Reddit', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-reddit',
				'class' => 'reddit',
			),

			'stumbleupon'	=> array(
				'title'       => esc_html__( 'StumbleUpon', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-stumbleupon',
				'class' => 'stumbleupon',
			),

			'tumblr' => array(
				'title'       => esc_html__( 'Tumblr', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-tumblr',
				'class' => 'tumblr',
			),

			'vimeo' => array(
				'title'       => esc_html__( 'Vimeo', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-vimeo',
				'class' => 'vimeo',
			),

			'wordpress' => array(
				'title'       => esc_html__( 'WordPress', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-wordpress',
				'class' => 'wordpress',
			),

			'yelp' => array(
				'title'       => esc_html__( 'Yelp', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-yelp',
				'class' => 'yelp',
			),

			'lastfm' => array(
				'title'       => esc_html__( 'Last.FM', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-lastfm',
				'class' => 'lastfm',
			),

			'dropbox' => array(
				'title'       => esc_html__( 'Dropbox', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-dropbox',
				'class' => 'dropbox',
			),

			'xing' => array(
				'title'       => esc_html__( 'Xing', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-xing',
				'class' => 'xing',
			),

			'deviantart' => array(
				'title'       => esc_html__( 'DeviantArt', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-deviantart',
				'class' => 'deviantart',
			),

			'apple' => array(
				'title'       => esc_html__( 'Apple', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-apple',
				'class' => 'apple',
			),

			'foursquare' => array(
				'title'       => esc_html__( 'Foursquare', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-foursquare',
				'class' => 'foursquare',
			),

			'github' => array(
				'title'       => esc_html__( 'GitHub', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-github',
				'class' => 'github',
			),

			'soundcloud' => array(
				'title'       => esc_html__( 'SoundCloud', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-soundcloud',
				'class'=> 'soundcloud',
			),

			'behance'	=> array(
				'title'       => esc_html__( 'Behance', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-behance',
				'class'=> 'behance',
			),

			'instagram' => array(
				'title'       => esc_html__( 'Instagram', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-instagram',
				'class'=> 'instagram',
			),

			'paypal' => array(
				'title'       => esc_html__( 'Paypal', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-paypal',
				'class'=> 'paypal',
			),

			'spotify' => array(
				'title' => esc_html__( 'Spotify', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-spotify',
				'class' => 'spotify',
			),

			'google_play'=> array(
				'title' => esc_html__( 'Google Play', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-play',
				'class' => 'google_play',
			),

			'px500' => array(
				'title' => esc_html__( '500px', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-500px',
				'class' => 'px500',
			),

			'vk' => array(
				'title' => esc_html__( 'vk.com', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-vk',
				'class' => 'vk',
			),

			'odnoklassniki' => array(
				'title' => esc_html__( 'Odnoklassniki', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-odnoklassniki',
				'class' => 'odnoklassniki',
			),

			'bitbucket'	=> array(
				'title' => esc_html__( 'Bitbucket', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-bitbucket',
				'class' => 'bitbucket',
			),

			'mixcloud' => array(
				'title' => esc_html__( 'Mixcloud', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-mixcloud',
				'class' => 'mixcloud',
			),

			'medium' => array(
				'title' => esc_html__( 'Medium', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-medium',
				'class' => 'medium',
			),

			'twitch' => array(
				'title' => esc_html__( 'Twitch', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-twitch',
				'class' => 'twitch',
			),

			'vine' => array(
				'title' => esc_html__( 'Vine', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-vine',
				'class' => 'vine',
			),

			'viadeo' => array(
				'title' => esc_html__( 'Viadeo', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-viadeo',
				'class' => 'viadeo',
			),

			'snapchat' => array(
				'title' => esc_html__( 'Snapchat', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-snapchat-ghost',
				'class' => 'snapchat',
			),

			'telegram' => array(
				'title' => esc_html__( 'Telegram', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-paper-plane',
				'class' => 'telegram',
			),

			'tripadvisor' => array(
				'title' => esc_html__( 'TripAdvisor', TIELABS_TEXTDOMAIN ),
				'icon'  => 'fa fa-tripadvisor',
				'class' => 'tripadvisor',
			),
		);

		return apply_filters( 'TieLabs/social_networks', $social_array );
	}
}


/*
 * Author social networks
 */
if( ! function_exists( 'tie_author_social_array' )){

	function tie_author_social_array(){

		$author_social = array(
			'facebook'    => array( 'text' => esc_html__( 'Facebook',  TIELABS_TEXTDOMAIN )),
			'twitter'     => array( 'text' => esc_html__( 'Twitter',   TIELABS_TEXTDOMAIN )),
			'google'      => array( 'text' => esc_html__( 'Google+',   TIELABS_TEXTDOMAIN ), 'icon' => 'google-plus' ),
			'linkedin'    => array( 'text' => esc_html__( 'LinkedIn',  TIELABS_TEXTDOMAIN )),
			'flickr'      => array( 'text' => esc_html__( 'Flickr',    TIELABS_TEXTDOMAIN )),
			'youtube'     => array( 'text' => esc_html__( 'YouTube',   TIELABS_TEXTDOMAIN )),
			'pinterest'   => array( 'text' => esc_html__( 'Pinterest', TIELABS_TEXTDOMAIN )),
			'behance'     => array( 'text' => esc_html__( 'Behance',   TIELABS_TEXTDOMAIN )),
			'instagram'   => array( 'text' => esc_html__( 'Instagram', TIELABS_TEXTDOMAIN )),
		);

		return apply_filters( 'TieLabs/author_social_array', $author_social );
	}
}


/*
 * Translations texts
 */
if( ! function_exists( 'tie_default_translation_texts' )){

	add_filter( 'TieLabs/translation_texts', 'tie_default_translation_texts' );
	function tie_default_translation_texts( $texts ){

		$default_texts = array(
			'Share'                  => esc_html__( 'Share', TIELABS_TEXTDOMAIN ),
			'No More Posts'          => esc_html__( 'No More Posts', TIELABS_TEXTDOMAIN ),
			'View all results'       => esc_html__( 'View all results', TIELABS_TEXTDOMAIN ),
			'Home'                   => esc_html__( 'Home', TIELABS_TEXTDOMAIN ),
			'Type and hit Enter'     => esc_html__( 'Type and hit Enter', TIELABS_TEXTDOMAIN ),
			'page'                   => esc_html__( 'page', TIELABS_TEXTDOMAIN ),
			'All'                    => esc_html__( 'All', TIELABS_TEXTDOMAIN ),
			'Previous page'          => esc_html__( 'Previous page', TIELABS_TEXTDOMAIN ),
			'Next page'              => esc_html__( 'Next page', TIELABS_TEXTDOMAIN ),
			'First'                  => esc_html__( 'First', TIELABS_TEXTDOMAIN ),
			'Last'                   => esc_html__( 'Last', TIELABS_TEXTDOMAIN ),
			'More'                   => esc_html__( 'More', TIELABS_TEXTDOMAIN ),
			'%s ago'                 => esc_html__( '%s ago', TIELABS_TEXTDOMAIN ),
			'Menu'                   => esc_html__( 'Menu', TIELABS_TEXTDOMAIN ),
			'Welcome'                => esc_html__( 'Welcome', TIELABS_TEXTDOMAIN ),
			'Pages'                  => esc_html__( 'Pages', TIELABS_TEXTDOMAIN ),
			'Categories'             => esc_html__( 'Categories', TIELABS_TEXTDOMAIN ),
			'Tags'                   => esc_html__( 'Tags', TIELABS_TEXTDOMAIN ),
			'Authors'                => esc_html__( 'Authors', TIELABS_TEXTDOMAIN ),
			'Archives'               => esc_html__( 'Archives', TIELABS_TEXTDOMAIN ),
			'Trending'               => esc_html__( 'Trending', TIELABS_TEXTDOMAIN ),
			'Via'                    => esc_html__( 'Via', TIELABS_TEXTDOMAIN ),
			'Source'                 => esc_html__( 'Source', TIELABS_TEXTDOMAIN ),
			'Views'                  => esc_html__( 'Views', TIELABS_TEXTDOMAIN ),
			'One Comment'            => esc_html__( 'One Comment', TIELABS_TEXTDOMAIN ),
			'%s Comments'            => esc_html__( '%s Comments', TIELABS_TEXTDOMAIN ),
			'Read More &raquo;'      => esc_html__( 'Read More &raquo;', TIELABS_TEXTDOMAIN ),
			'Share via Email'        => esc_html__( 'Share via Email', TIELABS_TEXTDOMAIN ),
			'Print'                  => esc_html__( 'Print', TIELABS_TEXTDOMAIN ),
			'About %s'               => esc_html__( 'About %s', TIELABS_TEXTDOMAIN ),
			'By %s'                  => esc_html__( 'By %s', TIELABS_TEXTDOMAIN ),
			'Popular'                => esc_html__( 'Popular', TIELABS_TEXTDOMAIN ),
			'Recent'                 => esc_html__( 'Recent', TIELABS_TEXTDOMAIN ),
			'Comments'               => esc_html__( 'Comments', TIELABS_TEXTDOMAIN ),
			'Search Results for: %s' => esc_html__( 'Search Results for: %s', TIELABS_TEXTDOMAIN ),
			'404 :('                 => esc_html__( '404 :(', TIELABS_TEXTDOMAIN ),
			'No products found'      => esc_html__( 'No products found', TIELABS_TEXTDOMAIN ),
			'Nothing Found'          => esc_html__( 'Nothing Found', TIELABS_TEXTDOMAIN ),
			'Dashboard'              => esc_html__( 'Dashboard', TIELABS_TEXTDOMAIN ),
			'Your Profile'           => esc_html__( 'Your Profile', TIELABS_TEXTDOMAIN ),
			'Log Out'                => esc_html__( 'Log Out', TIELABS_TEXTDOMAIN ),
			'Username'               => esc_html__( 'Username', TIELABS_TEXTDOMAIN ),
			'Password'               => esc_html__( 'Password', TIELABS_TEXTDOMAIN ),
			'Forget?'                => esc_html__( 'Forget?', TIELABS_TEXTDOMAIN ),
			'Remember me'            => esc_html__( 'Remember me', TIELABS_TEXTDOMAIN ),
			'Log In'                 => esc_html__( 'Log In', TIELABS_TEXTDOMAIN ),
			'Search for'             => esc_html__( 'Search for', TIELABS_TEXTDOMAIN ),
			'Price:'                 => esc_html__( 'Price:', TIELABS_TEXTDOMAIN ),
			'Quantity:'              => esc_html__( 'Quantity:', TIELABS_TEXTDOMAIN ),
			'Cart Subtotal:'         => esc_html__( 'Cart Subtotal:', TIELABS_TEXTDOMAIN ),
			'View Cart'              => esc_html__( 'View Cart', TIELABS_TEXTDOMAIN ),
			'Proceed to Checkout'    => esc_html__( 'Proceed to Checkout', TIELABS_TEXTDOMAIN ),
			'Go to the shop'         => esc_html__( 'Go to the shop', TIELABS_TEXTDOMAIN ),
			'Random Article'         => esc_html__( 'Random Article', TIELABS_TEXTDOMAIN ),
			'Follow'                 => esc_html__( 'Follow', TIELABS_TEXTDOMAIN ),
			'Check Also'             => esc_html__( 'Check Also', TIELABS_TEXTDOMAIN ),
			'Story Highlights'       => esc_html__( 'Story Highlights', TIELABS_TEXTDOMAIN ),
			'Subscribe'              => esc_html__( 'Subscribe', TIELABS_TEXTDOMAIN ),
			'Related Articles'       => esc_html__( 'Related Articles', TIELABS_TEXTDOMAIN ),
			'Read Next'              => esc_html__( 'Read Next', TIELABS_TEXTDOMAIN ),
			'Videos'                 => esc_html__( 'Videos', TIELABS_TEXTDOMAIN ),
			'Follow us on Flickr'    => esc_html__( 'Follow us on Flickr', TIELABS_TEXTDOMAIN ),
			'Follow Us'              => esc_html__( 'Follow Us', TIELABS_TEXTDOMAIN ),
			'Follow us on Twitter'   => esc_html__( 'Follow us on Twitter', TIELABS_TEXTDOMAIN ),
			'Less than a minute'     => esc_html__( 'Less than a minute', TIELABS_TEXTDOMAIN ),
			'%s hours read'          => esc_html__( '%s hours read', TIELABS_TEXTDOMAIN ),
			'1 minute read'          => esc_html__( '1 minute read', TIELABS_TEXTDOMAIN ),
			'%s minutes read'        => esc_html__( '%s minutes read', TIELABS_TEXTDOMAIN ),
			'No new notifications'   => esc_html__( 'No new notifications', TIELABS_TEXTDOMAIN ),
			'Notifications'          => esc_html__( 'Notifications', TIELABS_TEXTDOMAIN ),
			'Show More'              => esc_html__( 'Show More', TIELABS_TEXTDOMAIN ),
			'Load More'              => esc_html__( 'Load More', TIELABS_TEXTDOMAIN ),
			'Show Less'              => esc_html__( 'Show Less', TIELABS_TEXTDOMAIN ),

			'km/h'                   => esc_html__( 'km/h', TIELABS_TEXTDOMAIN ),
			'mph'                    => esc_html__( 'mph', TIELABS_TEXTDOMAIN ),
			'Thunderstorm'           => esc_html__( 'Thunderstorm', TIELABS_TEXTDOMAIN ),
			'Drizzle'                => esc_html__( 'Drizzle', TIELABS_TEXTDOMAIN ),
			'Light Rain'             => esc_html__( 'Light Rain', TIELABS_TEXTDOMAIN ),
			'Heavy Rain'             => esc_html__( 'Heavy Rain', TIELABS_TEXTDOMAIN ),
			'Rain'                   => esc_html__( 'Rain', TIELABS_TEXTDOMAIN ),
			'Snow'                   => esc_html__( 'Snow', TIELABS_TEXTDOMAIN ),
			'Mist'                   => esc_html__( 'Mist', TIELABS_TEXTDOMAIN ),
			'Clear Sky'              => esc_html__( 'Clear Sky', TIELABS_TEXTDOMAIN ),
			'Scattered Clouds'       => esc_html__( 'Scattered Clouds', TIELABS_TEXTDOMAIN ),

			'View your shopping cart'       => esc_html__( 'View your shopping cart', TIELABS_TEXTDOMAIN ),
			'Enter your Email address'      => esc_html__( 'Enter your Email address', TIELABS_TEXTDOMAIN ),
			"Don't have an account?"        => esc_html__( "Don't have an account?", TIELABS_TEXTDOMAIN ),
			'Your cart is currently empty.' => esc_html__( 'Your cart is currently empty.', TIELABS_TEXTDOMAIN ),

			'Oops! That page can&rsquo;t be found.'   => esc_html__( 'Oops! That page can&rsquo;t be found.', TIELABS_TEXTDOMAIN ),
			'Type your search words then press enter' => esc_html__( 'Type your search words then press enter', TIELABS_TEXTDOMAIN ),
			'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.'      => esc_html__( "It seems we can't find what you're looking for. Perhaps searching can help.", TIELABS_TEXTDOMAIN ),
			'Sorry, but nothing matched your search terms. Please try again with some different keywords.' => esc_html__( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', TIELABS_TEXTDOMAIN ),

			'Adblock Detected' => esc_html__( 'Adblock Detected', TIELABS_TEXTDOMAIN ),
			'Please consider supporting us by disabling your ad blocker' => esc_html__( 'Please consider supporting us by disabling your ad blocker', TIELABS_TEXTDOMAIN ),
		);

		if( ! empty( $texts ) && is_array( $texts ) ){
			$default_texts = array_merge( $texts, $default_texts );
		}

		return apply_filters( 'TieLabs/default_translation_texts', $default_texts );
	}
}
