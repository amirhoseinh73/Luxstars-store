<?php

add_action('customize_register', 'amh_nj_customizer_settings');
function amh_nj_customizer_settings($wp_customize)
{
    class amh_nj_Customize_Textarea_Control extends WP_Customize_Control
    {
        public $type = 'textarea';

        public function render_content()
        {
            ?>

            <label>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                <textarea rows="5"
                          style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea($this->value()); ?></textarea>
            </label>

            <?php
        }
    }

    //sections
    $wp_customize->add_section('favicon', array(
        'title' => __('Favicon', 'amh_nj'),
    ));
    $wp_customize->add_section('slider', array(
        'title' => __('SlideShow', 'amh_nj'),
    ));
    $wp_customize->add_section('service', array(
        'title' => __('Services', 'amh_nj'),
    ));
    $wp_customize->add_section('about_1', array(
        'title' => __('Part 1', 'amh_nj'),
    ));
    $wp_customize->add_section('about_2', array(
        'title' => __('Part 2', 'amh_nj'),
    ));
    $wp_customize->add_section('company', array(
        'title' => __('Customers', 'amh_nj'),
    ));
    $wp_customize->add_section('footer', array(
        'title' => __('Footer', 'amh_nj'),
    ));
    //favicon
    $wp_customize->add_setting('favicon', array('default' => get_template_directory_uri() . '/img/logo.png',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'favicon', array(
        'label' => __('Favicon'),
        'section' => 'favicon',
        'settings' => 'favicon',
    )));
    //slide show
    $wp_customize->add_setting('slider_1', array('default' => get_template_directory_uri() . '/img/slide-1.jpg',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'slider_1', array(
        'label' => __('اسلاید شو 1'),
        'section' => 'slider',
        'settings' => 'slider_1',
    )));
    $wp_customize->add_setting('slider_1_display', array(
        'default' => true,
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('slider_1_display', array(
        'label' => __('Show'),
        'section' => 'slider',
        'settings' => 'slider_1_display',
        'type' => 'radio',
        'choices' => array(
            'show' => __('Show'),
            'hide' => __('Hide'),
        ),
    ));
    $wp_customize->add_setting('slider_2', array('default' => get_template_directory_uri() . '/img/slide-2.jpg',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'slider_2', array(
        'label' => __('اسلاید شو 2'),
        'section' => 'slider',
        'settings' => 'slider_2',
    )));
    $wp_customize->add_setting('slider_2_display', array(
        'default' => true,
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('slider_2_display', array(
        'label' => __('Show'),
        'section' => 'slider',
        'settings' => 'slider_2_display',
        'type' => 'radio',
        'choices' => array(
            'show' => __('Show'),
            'hide' => __('Hide'),
        ),
    ));
    $wp_customize->add_setting('slider_3', array('default' => get_template_directory_uri() . '/img/slide-3.jpg',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'slider_3', array(
        'label' => __('اسلاید شو 3'),
        'section' => 'slider',
        'settings' => 'slider_3',
    )));
    $wp_customize->add_setting('slider_3_display', array(
        'default' => true,
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('slider_3_display', array(
        'label' => __('Show'),
        'section' => 'slider',
        'settings' => 'slider_3_display',
        'type' => 'radio',
        'choices' => array(
            'show' => __('Show'),
            'hide' => __('Hide'),
        ),
    ));
    $wp_customize->add_setting('slider_4', array('default' => get_template_directory_uri() . '/img/slide-4.jpg',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'slider_4', array(
        'label' => __('اسلاید شو 4'),
        'section' => 'slider',
        'settings' => 'slider_4',
    )));
    $wp_customize->add_setting('slider_4_display', array(
        'default' => true,
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('slider_4_display', array(
        'label' => __('Show'),
        'section' => 'slider',
        'settings' => 'slider_4_display',
        'type' => 'radio',
        'choices' => array(
            'show' => __('Show'),
            'hide' => __('Hide'),
        ),
    ));
    //service	
    $wp_customize->add_setting('service_1_title', array('default' => 'Title',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'service_1_title', array(
        'label' => __('Title') . ' 1',
        'section' => 'service',
        'settings' => 'service_1_title',
    )));
    $wp_customize->add_setting('service_1_text', array('default' => 'Text',));
    $wp_customize->add_control(new amh_nj_Customize_Textarea_Control($wp_customize, 'service_1_text', array(
        'label' => __('Text') . ' 1',
        'section' => 'service',
        'settings' => 'service_1_text',
    )));
    $wp_customize->add_setting('service_1_icon', array('default' => 'fas fa-cogs fa-3x',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'service_1_icon', array('label' => __('Icon') . ' 1', 'section' => 'service', 'settings' => 'service_1_icon',)));
    $wp_customize->add_setting('service_2_title', array('default' => 'Title',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'service_2_title', array(
        'label' => __('Title') . ' 2',
        'section' => 'service',
        'settings' => 'service_2_title',
    )));
    $wp_customize->add_setting('service_2_text', array('default' => 'Text',));
    $wp_customize->add_control(new amh_nj_Customize_Textarea_Control($wp_customize, 'service_2_text', array(
        'label' => __('Text') . ' 2',
        'section' => 'service',
        'settings' => 'service_2_text',
    )));
    $wp_customize->add_setting('service_2_icon', array('default' => 'fas fa-cogs fa-3x',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'service_2_icon', array('label' => __('آیکون') . ' 2', 'section' => 'service', 'settings' => 'service_2_icon',)));
    $wp_customize->add_setting('service_3_title', array('default' => 'Title',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'service_3_title', array(
        'label' => __('Title') . ' 3',
        'section' => 'service',
        'settings' => 'service_3_title',
    )));
    $wp_customize->add_setting('service_3_text', array('default' => 'Text',));
    $wp_customize->add_control(new amh_nj_Customize_Textarea_Control($wp_customize, 'service_3_text', array(
        'label' => __('Text') . ' 3',
        'section' => 'service',
        'settings' => 'service_3_text',
    )));
    $wp_customize->add_setting('service_3_icon', array('default' => 'fas fa-cogs fa-3x',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'service_3_icon', array('label' => __('آیکون') . ' 3', 'section' => 'service', 'settings' => 'service_3_icon',)));
    $wp_customize->add_setting('service_4_title', array('default' => 'Title',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'service_4_title', array(
        'label' => __('Title') . ' 4',
        'section' => 'service',
        'settings' => 'service_4_title',
    )));
    $wp_customize->add_setting('service_4_text', array('default' => 'Text',));
    $wp_customize->add_control(new amh_nj_Customize_Textarea_Control($wp_customize, 'service_4_text', array(
        'label' => __('Text') . ' 4',
        'section' => 'service',
        'settings' => 'service_4_text',
    )));
    $wp_customize->add_setting('service_4_icon', array('default' => 'fas fa-cogs fa-3x',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'service_4_icon', array('label' => __('آیکون') . ' 4', 'section' => 'service', 'settings' => 'service_4_icon',)));
    //about_1
    $wp_customize->add_setting('about_1_title_1', array('default' => 'Title',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'about_1_title_1', array(
        'label' => __('Title') . ' 1',
        'section' => 'about_1',
        'settings' => 'about_1_title_1',
    )));
    $wp_customize->add_setting('about_1_title_2', array('default' => 'Title',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'about_1_title_2', array(
        'label' => __('Title') . ' 2',
        'section' => 'about_1',
        'settings' => 'about_1_title_2',
    )));
    $wp_customize->add_setting('about_1_text', array('default' => 'Text',));
    $wp_customize->add_control(new amh_nj_Customize_Textarea_Control($wp_customize, 'about_1_text', array(
        'label' => __('Text'),
        'section' => 'about_1',
        'settings' => 'about_1_text',
    )));
    $wp_customize->add_setting('about_1_img', array('default' => get_template_directory_uri() . '/img/about.jpg',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'about_1_img', array(
        'label' => __('Image'),
        'section' => 'about_1',
        'settings' => 'about_1_img',
    )));
    $wp_customize->add_setting('about_1_tell', array('default' => '09123456789',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'about_1_tell', array(
        'label' => __('Mobile'),
        'section' => 'about_1',
        'settings' => 'about_1_tell',
    )));
    $wp_customize->add_setting('about_1_email', array('default' => 'info@amirhoseinhasani.ir',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'about_1_email', array(
        'label' => __('Email'),
        'section' => 'about_1',
        'settings' => 'about_1_email',
    )));
    $wp_customize->add_setting('about_1_chat', array('default' => '8960000586500',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'about_1_chat', array(
        'label' => __('Chat Id'),
        'section' => 'about_1',
        'settings' => 'about_1_chat',
    )));
    //about_2
    $wp_customize->add_setting('about_2_title_1', array('default' => 'Title',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'about_2_title_1', array(
        'label' => __('Title') . ' 1',
        'section' => 'about_2',
        'settings' => 'about_2_title_1',
    )));
    $wp_customize->add_setting('about_2_title_2', array('default' => 'Title',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'about_2_title_2', array(
        'label' => __('Title') . ' 2',
        'section' => 'about_2',
        'settings' => 'about_2_title_2',
    )));
    $wp_customize->add_setting('about_2_text', array('default' => 'Text',));
    $wp_customize->add_control(new amh_nj_Customize_Textarea_Control($wp_customize, 'about_2_text', array(
        'label' => __('Text'),
        'section' => 'about_2',
        'settings' => 'about_2_text',
    )));
    $wp_customize->add_setting('about_2_img', array('default' => get_template_directory_uri() . '/img/about.jpg',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'about_2_img', array(
        'label' => __('Image'),
        'section' => 'about_2',
        'settings' => 'about_2_img',
    )));
    //logos company
    $wp_customize->add_setting('company_title_1', array('default' => 'Title',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'company_title_1', array(
        'label' => __('Title') . ' 1',
        'section' => 'company',
        'settings' => 'company_title_1',
    )));
    $wp_customize->add_setting('company_title_2', array('default' => 'Title',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'company_title_2', array(
        'label' => __('Title') . ' 2',
        'section' => 'company',
        'settings' => 'company_title_2',
    )));
    $wp_customize->add_setting('company_img_1', array('default' => get_template_directory_uri() . '/img/logos1.png',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'company_img_1', array(
        'label' => __('Image') . ' 1',
        'section' => 'company',
        'settings' => 'company_img_1',
    )));
    $wp_customize->add_setting('company_img_1_display', array(
        'default' => true,
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('company_img_1_display', array(
        'label' => __('Show'),
        'section' => 'company',
        'settings' => 'company_img_1_display',
        'type' => 'radio',
        'choices' => array(
            'show' => __('Show'),
            'hide' => __('Hide'),
        ),
    ));
    $wp_customize->add_setting('company_img_2', array('default' => get_template_directory_uri() . '/img/logos2.png',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'company_img_2', array(
        'label' => __('Image') . ' 2',
        'section' => 'company',
        'settings' => 'company_img_2',
    )));
    $wp_customize->add_setting('company_img_2_display', array(
        'default' => true,
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('company_img_2_display', array(
        'label' => __('Show'),
        'section' => 'company',
        'settings' => 'company_img_2_display',
        'type' => 'radio',
        'choices' => array(
            'show' => __('Show'),
            'hide' => __('Hide'),
        ),
    ));
    $wp_customize->add_setting('company_img_3', array('default' => get_template_directory_uri() . '/img/logos3.png',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'company_img_3', array(
        'label' => __('Image') . ' 3',
        'section' => 'company',
        'settings' => 'company_img_3',
    )));
    $wp_customize->add_setting('company_img_3_display', array(
        'default' => true,
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('company_img_3_display', array(
        'label' => __('Show'),
        'section' => 'company',
        'settings' => 'company_img_3_display',
        'type' => 'radio',
        'choices' => array(
            'show' => __('Show'),
            'hide' => __('Hide'),
        ),
    ));
    $wp_customize->add_setting('company_img_4', array('default' => get_template_directory_uri() . '/img/logos4.png',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'company_img_4', array(
        'label' => __('Image') . ' 4',
        'section' => 'company',
        'settings' => 'company_img_4',
    )));
    $wp_customize->add_setting('company_img_4_display', array(
        'default' => true,
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('company_img_4_display', array(
        'label' => __('Show'),
        'section' => 'company',
        'settings' => 'company_img_4_display',
        'type' => 'radio',
        'choices' => array(
            'show' => __('Show'),
            'hide' => __('Hide'),
        ),
    ));
    $wp_customize->add_setting('company_img_5', array('default' => get_template_directory_uri() . '/img/logos5.png',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'company_img_5', array(
        'label' => __('Image') . ' 5',
        'section' => 'company',
        'settings' => 'company_img_5',
    )));
    $wp_customize->add_setting('company_img_5_display', array(
        'default' => true,
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('company_img_5_display', array(
        'label' => __('Show'),
        'section' => 'company',
        'settings' => 'company_img_5_display',
        'type' => 'radio',
        'choices' => array(
            'show' => __('Show'),
            'hide' => __('Hide'),
        ),
    ));
    $wp_customize->add_setting('company_img_6', array('default' => get_template_directory_uri() . '/img/logos6.png',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'company_img_6', array(
        'label' => __('Image') . ' 6',
        'section' => 'company',
        'settings' => 'company_img_6',
    )));
    $wp_customize->add_setting('company_img_6_display', array(
        'default' => true,
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('company_img_6_display', array(
        'label' => __('Show'),
        'section' => 'company',
        'settings' => 'company_img_6_display',
        'type' => 'radio',
        'choices' => array(
            'show' => __('Show'),
            'hide' => __('Hide'),
        ),
    ));
    $wp_customize->add_setting('company_img_7', array('default' => get_template_directory_uri() . '/img/logos7.png',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'company_img_7', array(
        'label' => __('Image') . ' 7',
        'section' => 'company',
        'settings' => 'company_img_7',
    )));
    $wp_customize->add_setting('company_img_7_display', array(
        'default' => true,
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('company_img_7_display', array(
        'label' => __('Show'),
        'section' => 'company',
        'settings' => 'company_img_7_display',
        'type' => 'radio',
        'choices' => array(
            'show' => __('Show'),
            'hide' => __('Hide'),
        ),
    ));
    $wp_customize->add_setting('company_img_8', array('default' => get_template_directory_uri() . '/img/logos8.png',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'company_img_8', array(
        'label' => __('Image') . ' 8',
        'section' => 'company',
        'settings' => 'company_img_8',
    )));
    $wp_customize->add_setting('company_img_8_display', array(
        'default' => true,
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('company_img_8_display', array(
        'label' => __('Show'),
        'section' => 'company',
        'settings' => 'company_img_8_display',
        'type' => 'radio',
        'choices' => array(
            'show' => __('Show'),
            'hide' => __('Hide'),
        ),
    ));
    $wp_customize->add_setting('company_img_9', array('default' => get_template_directory_uri() . '/img/logos9.png',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'company_img_9', array(
        'label' => __('Image') . ' 9',
        'section' => 'company',
        'settings' => 'company_img_9',
    )));
    $wp_customize->add_setting('company_img_9_display', array(
        'default' => true,
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('company_img_9_display', array(
        'label' => __('Show'),
        'section' => 'company',
        'settings' => 'company_img_9_display',
        'type' => 'radio',
        'choices' => array(
            'show' => __('Show'),
            'hide' => __('Hide'),
        ),
    ));
    $wp_customize->add_setting('company_img_10', array('default' => get_template_directory_uri() . '/img/logos10.png',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'company_img_10', array(
        'label' => __('Image') . ' 10',
        'section' => 'company',
        'settings' => 'company_img_10',
    )));
    $wp_customize->add_setting('company_img_10_display', array(
        'default' => true,
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('company_img_10_display', array(
        'label' => __('Show'),
        'section' => 'company',
        'settings' => 'company_img_10_display',
        'type' => 'radio',
        'choices' => array(
            'show' => __('Show'),
            'hide' => __('Hide'),
        ),
    ));
    //footer
    $wp_customize->add_setting('footer_logo', array('default' => get_template_directory_uri() . '/img/logo.png',));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'footer_logo', array(
        'label' => __('Image'),
        'section' => 'footer',
        'settings' => 'footer_logo',
    )));
    $wp_customize->add_setting('footer_title', array('default' => 'Title',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'footer_title', array(
        'label' => __('Title'),
        'section' => 'footer',
        'settings' => 'footer_title',
    )));
    $wp_customize->add_setting('footer_text', array('default' => 'Title',));
    $wp_customize->add_control(new amh_nj_Customize_Textarea_Control($wp_customize, 'footer_text', array(
        'label' => __('Text'),
        'section' => 'footer',
        'settings' => 'footer_text',
    )));
    $wp_customize->add_setting('footer_address', array('default' => 'آدرس شرکت ما: اصفهان، میدان امام حسین، جنب بانک ملی',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'footer_address', array(
        'label' => __('Address'),
        'section' => 'footer',
        'settings' => 'footer_address',
    )));
    $wp_customize->add_setting('footer_tell', array('default' => '031-31234567',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'footer_tell', array(
        'label' => __('Tell'),
        'section' => 'footer',
        'settings' => 'footer_tell',
    )));
    $wp_customize->add_setting('footer_mobile', array('default' => '09123456789',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'footer_mobile', array(
        'label' => __('Mobile'),
        'section' => 'footer',
        'settings' => 'footer_mobile',
    )));
    $wp_customize->add_setting('footer_email_1', array('default' => 'info@support.ir',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'footer_email_1', array(
        'label' => __('Email') . ' 1',
        'section' => 'footer',
        'settings' => 'footer_email_1',
    )));
    $wp_customize->add_setting('footer_email_2', array('default' => 'sale@support.ir',));
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'footer_email_2', array(
        'label' => __('Email') . ' 2',
        'section' => 'footer',
        'settings' => 'footer_email_2',
    )));
}
