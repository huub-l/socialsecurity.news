<?php
/**
 * Main Navigation
 *
 * This template can be overridden by copying it to your-child-theme/templates/header/nav-main.php.
 *
 * HOWEVER, on occasion TieLabs will need to update template files and you
 * will need to copy the new files to your child theme to maintain compatibility.
 *
 * @author 		TieLabs
 * @version   3.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


if( tie_get_option( 'main_nav' ) || tie_get_option( 'header_layout' ) == 1 ):

	// Header Layout
	$header_layout = tie_get_option( 'header_layout', 3 );

	$main_menu_class = 'main-nav header-nav';

	// Live Search skin
	$live_search_data_skin = '';
	if( tie_get_option( 'main-nav-components_search' ) && tie_get_option( 'main-nav-components_live_search' ) ){
		$live_search_skin      = tie_get_option( 'main_nav_dark' ) ? 'dark' : 'light';
		$main_menu_class      .= ' live-search-parent';
		$live_search_data_skin = 'data-skin="search-in-main-nav live-search-'. $live_search_skin .'"';
	}

	// Header Layout
	$logo_width 				= '';
	$header_line_height = '';
	$has_line_height 		= '';

	if( $header_layout == 1 ){

		$logo_args = tie_logo_args();

		extract( $logo_args );

		$logo_margin_top    = ! empty( $logo_margin_top )    ? $logo_margin_top    : 20; // Default value in the CSS file
		$logo_margin_bottom = ! empty( $logo_margin_bottom ) ? $logo_margin_bottom : 20; // Default value in the CSS file

		$logo_width			    = 'style="width:' . intval( $logo_width ). 'px"';
		$has_line_height 	  = ' has-line-height';
		$header_line_height = 'style="line-height:' . intval( $logo_height + $logo_margin_top + $logo_margin_bottom ). 'px"';
	}

?>

<div class="main-nav-wrapper">
	<nav id="main-nav" <?php echo ( $live_search_data_skin ); ?> class="<?php echo esc_attr( $main_menu_class ) ?>" <?php echo ( $header_line_height ) ?> aria-label="<?php esc_html_e( 'Primary Navigation', TIELABS_TEXTDOMAIN ); ?>">
		<div class="container">

			<div class="main-menu-wrapper">

				<?php
					if( $header_layout == 1 ){ ?>
						<div class="header-layout-1-logo <?php echo esc_attr( $has_line_height ) ?>" <?php echo ( $logo_width ) ?>>
							<?php

								do_action( 'TieLabs/Logo/before' );

								tie_logo();

								do_action( 'TieLabs/Logo/after' );
							?>
						</div>
						<?php
					}
				?>

				<div id="menu-components-wrap">

					<?php

						// Sticky Menu Logo
						tie_sticky_logo();

					?>

					<div class="main-menu main-menu-wrap tie-alignleft">
						<?php

							$custom_menu = tie_get_object_option( false, 'cat_menu', 'tie_menu' );

							$menu_args   = array(
								'menu'            => $custom_menu,
								'container_id'    => 'main-nav-menu',
								'container_class' => 'main-menu header-menu',
								'theme_location'  => 'primary',
								'fallback_cb'     => false,
								'items_wrap'      => '<ul id="%1$s" class="%2$s" role="menubar">%3$s</ul>',
							);

							// Check if the Built-in theme mega menu is enabled
							if( ! tie_get_option( 'disable_mega_menu' ) ){
								$menu_args['walker'] = new TIELABS_MEGA_MENU();
							}

							wp_nav_menu( $menu_args );

						?>
					</div><!-- .main-menu.tie-alignleft /-->

					<?php

						do_action( 'TieLabs/after_main_menu' );

						// Get components template
						TIELABS_HELPER::get_template_part( 'templates/header/components', '', array( 'components_id' => 'main-nav' ) );

						do_action( 'TieLabs/after_main_components' );

					?>

				</div><!-- #menu-components-wrap /-->
			</div><!-- .main-menu-wrapper /-->
		</div><!-- .container /-->
	</nav><!-- #main-nav /-->
</div><!-- .main-nav-wrapper /-->

<?php endif; ?>
