<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
// do_action( 'woocommerce_before_main_content' );

?>
<header class="woocommerce-products-header">
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
	<?php endif; ?>

	<?php
	/**
	 * Hook: woocommerce_archive_description.
	 *
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description - 10
	 */
	do_action( 'woocommerce_archive_description' );
	?>
</header>
<?php
$show_category = false;

$taxonomy     = 'product_cat';
$orderby      = 'name';  
$show_count   = 0;      // 1 for yes, 0 for no
$pad_counts   = 0;      // 1 for yes, 0 for no
$hierarchical = 1;      // 1 for yes, 0 for no  
$title        = '';  
$empty        = 0;

$args = array(
	'taxonomy'     => $taxonomy,
	'orderby'      => $orderby,
	'show_count'   => $show_count,
	'pad_counts'   => $pad_counts,
	'hierarchical' => $hierarchical,
	'title_li'     => $title,
	'hide_empty'   => $empty
);
$all_categories = get_categories( $args );
foreach ($all_categories as $cat) {
	if( $cat->category_parent != 0 ) continue;
	if ( trim( woocommerce_page_title( false ) ) !== strtolower( trim( $cat->name ) ) ) continue;
	$show_category = true;

	$category_id = $cat->term_id;
	// echo '<a href="'. get_term_link($cat->slug, 'product_cat') .'">'. $cat->name .'</a>';
	$thumbnail_id = get_term_meta( $category_id, 'thumbnail_id', true );
	$image = wp_get_attachment_url( $thumbnail_id );
	if ( $image ) {
		echo '<img class="img-cat-icon-page-title" src="' . $image . '" alt="' . $cat->name . '" />';
	}

	$args2 = array(
		'taxonomy'     => $taxonomy,
		'child_of'     => 0,
		'parent'       => $category_id,
		'orderby'      => $orderby,
		'show_count'   => $show_count,
		'pad_counts'   => $pad_counts,
		'hierarchical' => $hierarchical,
		'title_li'     => $title,
		'hide_empty'   => $empty
	);
	$sub_cats = get_categories( $args2 );
	
	if ( ! $sub_cats ) break;
	echo '<div class="row">';
	foreach( $sub_cats as $sub_category ) {
		$thumbnail_id = get_term_meta( $sub_category->term_id, 'thumbnail_id', true );
	    $image = wp_get_attachment_url( $thumbnail_id );
		$image_default_url = home_url( "wp-content/themes/store/img/no-icons.png" );

		echo '<div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-3 conts-parent mb-boot-15 px-2">
			<div class="conts">
				<a href="' . $sub_category->slug . '" class="blog-link-absolute"></a>
				<div class="content-one img-thumbnail">
					<span class="cont-icon-rotate"><img src="' . ( $image ? $image : $image_default_url ) . '"/></span>
					<div class="caption p-2">
						<h1 class="font-19 fw500 text-center text-boot-4">' . $sub_category->name . '</h1>
						<p class="font-13 fw400 text-justify lh-18 text-secondary">
						' . $sub_category->description . '
						</p>
						<p class="font-11 fw400 text-center lh-18 text-boot-4">
						تعداد محصولات
						' . $sub_category->count . '
						</p>
					</div>
				</div>
			</div>
		</div>';
	}
	echo '</div>';
}
if ( ! $show_category ) {
	if ( woocommerce_product_loop() ) {

		/**
		 * Hook: woocommerce_before_shop_loop.
		 *
		 * @hooked woocommerce_output_all_notices - 10
		 * @hooked woocommerce_result_count - 20
		 * @hooked woocommerce_catalog_ordering - 30
		 */
		do_action( 'woocommerce_before_shop_loop' );

		woocommerce_product_loop_start();

		if ( wc_get_loop_prop( 'total' ) ) {
			while ( have_posts() ) {
				the_post();

				/**
				 * Hook: woocommerce_shop_loop.
				 */
				do_action( 'woocommerce_shop_loop' );
				wc_get_template_part( 'content', 'product' );
			}
		}

		woocommerce_product_loop_end();

		/**
		 * Hook: woocommerce_after_shop_loop.
		 *
		 * @hooked woocommerce_pagination - 10
		 */
		do_action( 'woocommerce_after_shop_loop' );
	} else {
		/**
		 * Hook: woocommerce_no_products_found.
		 *
		 * @hooked wc_no_products_found - 10
		 */
		do_action( 'woocommerce_no_products_found' );
	}
}
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
// do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
