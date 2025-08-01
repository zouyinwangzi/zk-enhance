<?php

/**
 * 根据options的值，执行相应的操作
 */

if (!defined('ABSPATH')) {
    exit();
}

//获取所有设置
$zk_option = ZK_OPTION::getInstance();
$zk_option_all = $zk_option->get_all();

// 禁用自动生成的图片尺寸
if ($zk_option_all['zkcc_disable_builtin_size'] == '1') {
    add_filter('intermediate_image_sizes_advanced', function ($sizes) {
        global $zk_option_all;
        $disable_image_size = $zk_option_all['zkcc_disable_image_size'];

        foreach (array_keys($sizes) as $key) {
            if (in_array($key, $disable_image_size)) {
                unset($sizes[$key]);
            }
        }

        return $sizes;
    }, 20, 1);
} //disable builtin size
