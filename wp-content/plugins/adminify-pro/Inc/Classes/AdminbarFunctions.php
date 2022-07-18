<?php
// admin bar

function jltwp_adminify_admin_bar()
{
    global $wp_admin_bar;

    $all_toolbar_nodes = $wp_admin_bar->get_nodes();
    $site = array();
    foreach ($all_toolbar_nodes as $key => $node) {
        $args = $node;
        if ($args->id == "site-name" || $args->id == "visit-site") {
            $logo = "";
            if (is_admin()) {
                $logo = $this->setting->get_setting('bar_logo') ? sprintf('<img src="%s">', $this->setting->get_setting('bar_logo')) : '';
            }
            $hide = $this->setting->get_setting('bar_name_hide') ? "hide" : "";
            $name = $this->setting->get_setting('bar_name') ? $this->setting->get_setting('bar_name') : $args->title;
            $args->title = sprintf('%s <span class="%s">%s</span>', $logo, $hide, $name);
            $this->setting->get_setting('bar_name_link') && ($args->href = $this->setting->get_setting('bar_name_link'));
        }
        if ($args->id == "my-sites") {
            $site = $node;
        }
        // update the Toolbar node
        $wp_admin_bar->add_node($args);
    }
    // remove the wordpress logo
    $wp_admin_bar->remove_node('wp-logo');
    $wp_admin_bar->remove_node('view-site');

    $wp_admin_bar->remove_node('my-sites');
    $wp_admin_bar->add_node($site);

    if ($this->setting->get_setting('bar_updates_hide')) {
        $wp_admin_bar->remove_node('updates');
    }
    if ($this->setting->get_setting('bar_comments_hide')) {
        $wp_admin_bar->remove_node('comments');
    }
    if ($this->setting->get_setting('bar_new_hide')) {
        $wp_admin_bar->remove_node('new-content');
    }
    if ($this->setting->get_setting('bar_site_hide')) {
        $wp_admin_bar->remove_node('my-sites');
    }
}
