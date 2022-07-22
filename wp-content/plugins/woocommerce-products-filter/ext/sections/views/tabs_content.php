<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOF;
$woof_settings = $WOOF->settings;
?>

<section id="tabs-sections">

    <div class="woof-tabs woof-tabs-style-line">

        <?php global $wp_locale; ?>

        <div class="content-wrap">

            <section>


                <a href="https://products-filter.com/extencion/sections/" target="_blank" class="button-primary"><?php echo __('About extension', 'woocommerce-products-filter') ?></a><br />
                <br />

                <p class="description"><?php _e("Allows to wrap filter-elements into [close/open]-sections and make filter form more compact.", 'woocommerce-products-filter') ?></p>


                <div class="woof-control-section">

                    <h5><?php _e("Init sections", 'woocommerce-products-filter') ?></h5>

                    <div class="woof-control-container">
                        <div class="woof-control">

                            <?php
                            $init_sections = array(
                                0 => __("No", 'woocommerce-products-filter'),
                                1 => __("Yes", 'woocommerce-products-filter'),
                            );
                            ?>
                            <?php
                            if (!isset($woof_settings['woof_init_sections']) OR empty($woof_settings['woof_init_sections'])) {
                                $woof_settings['woof_init_sections'] = 0;
                            }
                            ?>
                            <div class="select-wrap">
                                <select name="woof_settings[woof_init_sections]" class="chosen_select slideout_value" data-name="woof_init_sections">
                                    <?php foreach ($init_sections as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($woof_settings['woof_init_sections'] == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e("Init sections by default", 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->

                <div class="woof-control-section">

                    <h5><?php _e("Sections behavior", 'woocommerce-products-filter') ?></h5>

                    <div class="woof-control-container">
                        <div class="woof-control">

                            <?php
                            $sections_behavior = array(
                                'tabs_checkbox' => __("As checkbox", 'woocommerce-products-filter'),
                                'tabs_radio' => __("As radio", 'woocommerce-products-filter'),
                            );
                            ?>
                            <?php
                            if (!isset($woof_settings['sections_type']) OR empty($woof_settings['sections_type'])) {
                                $woof_settings['sections_type'] = 'tabs_checkbox';
                            }
                            ?>
                            <div class="select-wrap">
                                <select name="woof_settings[sections_type]" class="chosen_select slideout_value" data-name="woof_sections_type">
                                    <?php foreach ($sections_behavior as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($woof_settings['sections_type'] == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e("Behavior of how the filter sections will open.", 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->
                <div class="woof-control-section">

                    <h4><?php _e('Add section', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container ">

                        <div class="woof-control-container ">
                            <div class="woof_sections_list_container">
                                <ul id='woof_sections_list'>
                                    <?php
                                    $sections = array();

                                    if (isset($woof_settings['sections']) && is_array($woof_settings['sections'])) {
                                        $sections = $woof_settings['sections'];
                                    }

                                    foreach ($sections as $key => $data) {
                                        $ext_sections->woof_draw_sctions_item($key, $data['title'], $data['from'], $data['to']);
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <div class="woof-control ">
                            <input type="button" class="woof_add_sections woof-button" style="margin: 0;" value="<?php _e('Create section', 'woocommerce-products-filter') ?>">
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e('Create new section. Don`t forget to click on the save button.', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->

                <div class="woof-control-section">

                    <div class="woof-control-container">
                        <div class="woof-control">
                            <input type="button" class="woof-button" style="margin: 0;" id="woof_sections_generate" value="<?php _e("Generate attributes for shortcode [woof]", 'woocommerce-products-filter') ?>">
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e("This button is just helper which allows to assemble data for [woof] shortcode.", 'woocommerce-products-filter') ?></p>
                            <span class="woof_sections_shortcode_res" style="color: cornflowerblue;"></span>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->

            </section>

        </div>

    </div>
</section>
