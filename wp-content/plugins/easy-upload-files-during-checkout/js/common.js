

jQuery(document).ready(function($){

    $('body').on('click', 'div .eufdc-dashicons', function(e){

            var this_li = $(this).parents("li:first");
            var all_other_li = $('div .eufdc-dashicons').parents('ol:first, ul:first').find('li').not(this_li);
            all_other_li.find('.eufdc_file_caption_wrapper').slideUp();
            this_li.find('.eufdc_file_caption_wrapper').slideToggle();

            var this_show_icon = this_li.find('.eufdc-dashicons.eufdc-dash-show');
            var this_hide_icon = this_li.find('.eufdc-dashicons.eufdc-dash-hide');

            this_show_icon.toggle();
            this_hide_icon.toggle();

            $.each(all_other_li, function(){

                var show_icon = $(this).find('.eufdc-dashicons.eufdc-dash-show');
                var hide_icon = $(this).find('.eufdc-dashicons.eufdc-dash-hide');
                hide_icon.hide();
                show_icon.show();

            });

            $('.eufdc-dashicons:visible').css('display', 'inline-block');


        });

});