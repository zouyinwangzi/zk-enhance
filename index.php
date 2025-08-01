<?php
/*
Plugin Name: Zendkee Enhance
Plugin URI: #
Description: This Plugin enhances the functionality of WordPress.
Author: Zendkee
Version: 1.0
Author URI: #
*/


define('ZK_ENHANCE_VERSION', '1.0');
define('ZK_ENHANCE_PATH', plugin_dir_path(__FILE__));
define('ZK_ENHANCE_URL', plugin_dir_url(__FILE__));
define('ZK_ENHANCE_BASENAME', plugin_basename(__FILE__));
define('ZK_ENHANCE_FLAG', dirname(ZK_ENHANCE_BASENAME));


// 在其他插件的主文件或初始化代码中添加
if (!is_plugin_active('zkcc-framework/load.php')) {
    // 框架未激活，给出后台提示
    add_action('admin_notices', function () {
        echo '<div class="notice notice-error"><p>ZKCC Framework 未激活，请先安装并激活该框架插件。</p></div>';
    });
    return; // 阻止后续代码执行
}

// 框架已激活，可以安全引用
require_once WP_PLUGIN_DIR . '/zkcc-framework/load.php';
require_once __DIR__ . '/includes/backend.php';
require_once __DIR__ . '/includes/run.php';
