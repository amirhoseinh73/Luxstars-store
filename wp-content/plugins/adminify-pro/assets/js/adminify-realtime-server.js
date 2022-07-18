// WP Adminify Realtime Server Details widget
(function ($)
{
    'use strict';

    var set_default = false;

    var Adminify_Realtime_Server={

        // Server Uptime Counter
        ServerUptime: function(upsec){

            const second = 1000,
                minute = second * 60,
                hour = minute * 60,
                day = hour * 24;

            var session_time,
                get_session_time;

            get_session_time = localStorage.getItem('adminify_server_uptime');

            if (get_session_time === null) {
                 session_time = get_session_time;
            } else{
                session_time = upsec;
            }


            var adminifyUptime = session_time,
                countUp = new Date(adminifyUptime).getTime(),
                x = setInterval(function () {

                    var now = new Date().getTime(),
                    distance = now - countUp;

                    document.getElementById("adminify-days").innerText = Math.floor(distance / (day)),
                    document.getElementById("adminify-hours").innerText = Math.floor((distance % (day)) / (hour)),
                    document.getElementById("adminify-minutes").innerText = Math.floor((distance % (hour)) / (minute)),
                    document.getElementById("adminify-seconds").innerText = Math.floor((distance % (minute)) / second);

                    //do something later when date is reached
                    if (distance < 0) {
                            countUp = document.getElementById("adminify-countdown"),
                        clearInterval(x);
                    }

                    localStorage.setItem('adminify_server_uptime', adminifyUptime);
                //seconds
            }, 10);

            // setInterval(adminifyUptime, 1000);
        },


        ProgressBar: function(){
            var totalProgress, progress;
            const circles = document.querySelectorAll('.adminify-progress-bar');
            for(var i = 0; i < circles.length; i++) {
                totalProgress = circles[i].querySelector('circle').getAttribute('stroke-dasharray');
                progress = circles[i].parentElement.getAttribute('data-percent');

                circles[i].querySelector('.adminify-bar').style['stroke-dashoffset'] = totalProgress * progress / 100;

            }
        },


        Ajax_Server_Data: function(){
            $.ajax({
                type: 'POST',
                dataType: 'json',
                async: false,
                url: WPAdminify_Server.ajax_url,
                data: {
                    action: 'adminify_live_server_stats',
                    security: WPAdminify_Server.security_nonce
                },
                cache: false,
                success: function (response) {

                    // CPU Loads
                    var $cpu_load = response.cpu_load;
                    $('#adminify-cpu-load').attr('data-percent', $cpu_load);

                    if ($cpu_load <= 10) {
                        $('#adminify-cpu-load .adminify-progress-bar circle:not(.adminify-bar)').css({ 'stroke': '#00BA88' });
                    } else if ($cpu_load > 65 && $cpu_load < 90) {
                        $('#adminify-cpu-load .adminify-progress-bar circle:not(.adminify-bar)').css({ 'stroke': '#ffe08a' });
                    } else if ($cpu_load > 90) {
                        $('#adminify-cpu-load .adminify-progress-bar circle:not(.adminify-bar)').css({ 'stroke': '#f14668' });
                    }


                    // PHP Memory Usage
                    var $memory_load_mb = response.memory_usage_MB;
                    $('#adminify-php-memory-usage').attr('data-percent', $memory_load_mb);

                    if ($memory_load_mb <= 10) {
                        $('#adminify-php-memory-usage .adminify-progress-bar circle:not(.adminify-bar)').css({ 'stroke': '#00BA88' });
                    } else if ($memory_load_mb > 65 && $memory_load_mb < 90) {
                        $('#adminify-php-memory-usage .adminify-progress-bar circle:not(.adminify-bar)').css({ 'stroke': '#ffe08a' });
                    } else if ($memory_load_mb > 90) {
                        $('#adminify-php-memory-usage .adminify-progress-bar circle:not(.adminify-bar)').css({ 'stroke': '#f14668' });
                    }


                    // RAM Usage
                    var $ram_used = response.used_ram;
                    $('#adminify-ram-usage').attr('data-percent', $ram_used);


                    if ($ram_used <= 10) {
                        $('#adminify-ram-usage .adminify-progress-bar circle:not(.adminify-bar)').css({ 'stroke': '#00BA88' });
                    } else if ($ram_used > 65 && $ram_used < 90) {
                        $('#adminify-ram-usage .adminify-progress-bar circle:not(.adminify-bar)').css({ 'stroke': '#ffe08a' });
                    } else if ($ram_used > 90) {
                        $('#adminify-ram-usage .adminify-progress-bar circle:not(.adminify-bar)').css({ 'stroke': '#f14668' });
                    }



                    // WP Memory Usage
                    var $wp_memory_usage = response.wp_memory_usage;
                    $('#adminify-wp-memory-usage').attr('data-percent', $wp_memory_usage);


                    if ($wp_memory_usage <= 10) {
                        $('#adminify-wp-memory-usage .adminify-progress-bar circle:not(.adminify-bar)').css({ 'stroke': '#00BA88' });
                    } else if ($wp_memory_usage > 65 && $wp_memory_usage < 90) {
                        $('#adminify-wp-memory-usage .adminify-progress-bar circle:not(.adminify-bar)').css({ 'stroke': '#ffe08a' });
                    } else if ($wp_memory_usage > 90) {
                        $('#adminify-wp-memory-usage .adminify-progress-bar circle:not(.adminify-bar)').css({ 'stroke': '#f14668' });
                    }


                    Adminify_Realtime_Server.ProgressBar();

                    if (set_default == false) {
                        Adminify_Realtime_Server.ServerUptime(response.uptime);
                        set_default = true;
                    }

                },
            });
        }
    }

    // Documents Loaded
    setInterval(Adminify_Realtime_Server.Ajax_Server_Data, 5000);
})(jQuery);
