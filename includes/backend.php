<?php
// 插件列表加入设置链接的入口
if (is_admin()) {
    add_filter('plugin_action_links_' . ZK_ENHANCE_BASENAME, function ($links) {
        $plugin_links = array(
            '<a href="' . admin_url('options-general.php?page=' . ZKCC_FLAG) . '">' . __('Settings', 'zendkee') . '</a>',
        );
        return array_merge($plugin_links, $links);
    });
}

//引入js
add_action('admin_enqueue_scripts', function () {
    if (strpos($_SERVER['REQUEST_URI'], ZKCC_FLAG) !== false) {
        
        // wp_enqueue_style(ZK_ENHANCE_FLAG.'-css', plugin_dir_url(__DIR__) . 'css/backend.css', [], ZK_ENHANCE_VERSION);

        //引入本插件下的js文件js/backend.js
        wp_enqueue_script(ZK_ENHANCE_FLAG.'-js', plugin_dir_url(__DIR__) . 'js/backend.js', array('jquery'), ZK_ENHANCE_VERSION);

    }
});


/** 后台设置项表单功能（关键功能）
 * 
 * $common_tab，是一个数组，包含：
 *      id:tab标签的id值，由字母、数字组成
 *      label:tab标签的名字，随便起
 *      content:该tab标签下的正文内容，主要用于展示后台表单选项功能，由html构成
 * 
 * $all_options，是已经保存到数据库中的各个设置项值，是一个数组，包含所有zkcc_开头的自定义键值对
 * 
 * 返回值：$common_tab
**/

add_filter('zkcc_tabs', function ($common_tab, $all_options) {

    //定义本插件/templates目录作为twig模板目录
    $framework_twig = new FRAMEWORK_TWIG(dirname(__DIR__) . '/templates');
    $context['options'] = $all_options;
    $context['new_sizes'] = wp_get_registered_image_subsizes();
    //将misc.twig模板渲染后的html赋值给$content
    $content =  $framework_twig->render('misc.twig', $context);

    $common_tab[] = array(
        'id' => 'misc',
        'label' => __('Misc', 'zendkee'),
        'content' => $content,
    );

    //返回过滤后的$common_tab
    return $common_tab;
}, 10, 2);


