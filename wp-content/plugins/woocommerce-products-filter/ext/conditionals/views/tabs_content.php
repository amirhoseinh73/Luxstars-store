<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOF;
?>

<section id="tabs-conditionals">

    <div class="woof-tabs woof-tabs-style-line">

        <?php global $wp_locale; ?>

        <div class="content-wrap">

            <section>
                <div class="woof-tabs woof-tabs-style-line">
                    <?php
                    if (!isset($woof_settings['woof_conditionals'])) {
                        $woof_settings['woof_conditionals'] = "";
                    }
                    ?>
                    <div class="woof-control-section" >
                        <h4><?php _e('Data for default filter', 'woocommerce-products-filter') ?></h4>

                        <div class="woof-control-container">
                            <div class="woof-control">

                                <?php if ($WOOF->is_free_ver): ?>
                                    <p style="color: red;"><?php _e('In FREE version it is possible to operate with 1 condition (first) only!', 'woocommerce-products-filter') ?></p>
                                <?php endif; ?>

                                <textarea style="width: 100%; height: 150px;" name="woof_settings[woof_conditionals]"  data-name="woof_conditionals"><?php echo $woof_settings['woof_conditionals'] ?></textarea>

                            </div>
                            <div class="woof-description">
                                <p class="description"><?php _e('You can define the conditions for displaying filter elements depending of the current filtering request. Briefly this feature allows to hide some filter-elements while others are not selected. Or vice versa - show some filter-elements if some another filter elements are selected.', 'woocommerce-products-filter') ?></p>
                                <p class="description"><?php _e('Syntax example: product_cat>pa_size,by_instock>pa_color. In the example described: if a user will select a [product category] then filter by [pa_size] or [by_instock] will be appeared [pa_color]. For shortcode [woof] use an attribute: [conditionals]. Use line break in the textarea to define some rules (press Enter keyboard). In [woof] shortcode to define some rules use sign: [+].', 'woocommerce-products-filter') ?></p>
                                <br />
                                <hr />

                                <h4><?php _e('Possible keys', 'woocommerce-products-filter') ?>:</h4>
                                <p class="description" style="color: #777; line-height: 27px;">
                                    <?php
                                    global $WOOF;
                                    $taxonomies = $WOOF->get_taxonomies();
                                    $taxonomies_keys = array_keys($taxonomies);
                                    $keys = [];

                                    $standard_filters = array(
                                        'by_price',
                                        'by_rating',
                                        'by_sku',
                                        'by_text',
                                        'by_author',
                                        'by_backorder',
                                        'by_featured',
                                        'by_instock',
                                        'by_onsales',
                                        'products_messenger',
                                        'query_save'
                                    );

                                    foreach ($standard_filters as $key) {
                                        $keys[] = $key;
                                    }
                                    foreach ($taxonomies_keys as $key) {
                                        $keys[] = $key;
                                    }

                                    sort($keys);
                                    $keys = implode(', ', $keys);
                                    echo $keys;
                                    ?>
                                </p>

                            </div>
                        </div>
                    </div><!--/ .woof-control-section-->  				

                </div>

            </section>

        </div>

    </div>
</section>


