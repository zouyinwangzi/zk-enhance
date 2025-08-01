# Zendkee Enhance 插件

## 插件简介

**Zendkee Enhance** 是一个为 WordPress 提供增强功能的插件，依赖于 [ZKCC Framework](../zkcc-framework) 插件作为底层后台管理和数据处理框架。
主要通过这个插件讲解它如何利用zkcc-framework框架进行插件开发。



## 目录结构

```
zk-enhance/
├── includes/
├── index.php
├── js/
├── templates/
└── ...
```

## 代码讲解
**index.php**
```php
//定义常量
define('ZK_ENHANCE_VERSION', '1.0');
define('ZK_ENHANCE_PATH', plugin_dir_path(__FILE__));
define('ZK_ENHANCE_URL', plugin_dir_url(__FILE__));
define('ZK_ENHANCE_BASENAME', plugin_basename(__FILE__));
define('ZK_ENHANCE_FLAG', dirname(ZK_ENHANCE_BASENAME));
```

```php
// 在其他插件的主文件或初始化代码中添加
if (!is_plugin_active('zkcc-framework/load.php')) {
    // 框架未激活，给出后台提示
    add_action('admin_notices', function () {
        echo '<div class="notice notice-error"><p>ZKCC Framework 未激活，请先安装并激活该框架插件。</p></div>';
    });
    return; // 阻止后续代码执行
}
```

```php
// 引入框架文件
require_once WP_PLUGIN_DIR . '/zkcc-framework/load.php';
```

```php
//引入本插件后台文件
require_once __DIR__ . '/includes/backend.php';
//引入本插件运行文件
require_once __DIR__ . '/includes/run.php';
```


**includes/backend.php文件**
```php
// 后台插件列表加入设置链接的入口
if (is_admin()) {
    add_filter('plugin_action_links_' . ZK_ENHANCE_BASENAME, function ($links) {
        $plugin_links = array(
            '<a href="' . admin_url('options-general.php?page=' . ZKCC_FLAG) . '">' . __('Settings', 'zendkee') . '</a>',
        );
        return array_merge($plugin_links, $links);
    });
}
```

```php
//后台引入本插件的js/css
add_action('admin_enqueue_scripts', function () {
    if (strpos($_SERVER['REQUEST_URI'], ZKCC_FLAG) !== false) {
        
        // wp_enqueue_style(ZK_ENHANCE_FLAG.'-css', plugin_dir_url(__DIR__) . 'css/backend.css', [], ZK_ENHANCE_VERSION);

        //引入本插件下的js文件js/backend.js
        wp_enqueue_script(ZK_ENHANCE_FLAG.'-js', plugin_dir_url(__DIR__) . 'js/backend.js', array('jquery'), ZK_ENHANCE_VERSION);

    }
});
```


```php
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
```


**includes/run.php**
```php
//获取所有设置
$zk_option = ZK_OPTION::getInstance();
$zk_option_all = $zk_option->get_all();

// 禁用自动生成的图片尺寸（这是本插件的功能）
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
```




**templates/misc.twig**
```twig
<!--
这里是后台设置项的主要html结构，使用twig模板做主框架
html代码使用layui风格所写
checkbox类型的input表单，在需要保存数据的表单项中，要加入class="layui-checkbox-handle"，系统自动检测该类下的表单组件，并在没有选中的状态下发送字符串0到后台，确保后台能获取到该值（未选中状态）
-->
<div class="layui-collapse" lay-accordion>

    <div class="layui-colla-item">
        <h2 class="layui-colla-title">图像管理</h2>

        <div class="layui-colla-content">
            <h3>生成图像尺寸管理</h3>
            <div class="layui-form-item">
                <label class="layui-form-label">禁止生成图像尺寸</label>
                <div class="layui-input-block">
                    <input type="checkbox" name="zkcc_disable_builtin_size" value="1" lay-skin="switch" class="layui-checkbox-handle"
                        lay-text="ON|OFF" {% if options.zkcc_disable_builtin_size=='1' %}checked{% endif %}>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">全选/全不选</label>
                <div class="layui-input-block">
                    <input type="checkbox" name="image_size_select_all" lay-filter="image_size_select_all" value="1"
                        lay-skin="switch" lay-text="Select All|Select None">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">禁用以下图像尺寸</label>
                <div class="layui-input-block disable_image_size">
                    {% for name, size in new_sizes %}
                    <input type="checkbox" name="zkcc_disable_image_size[]" lay-skin="primary"
                        title="{{ name ~ ': ' ~ size.width ~ ' * ' ~ size.height }}" value="{{ name }}" {% if name in
                        options.zkcc_disable_image_size %}checked{% endif %} />
                    {% endfor %}

                </div>
            </div>




            <hr class="layui-bg-blue">

            <h3>支持webp格式图片</h3>
            <div class="layui-form-item">
                <label class="layui-form-label">开启支持</label>
                <div class="layui-input-block">
                    <input type="checkbox" name="zkcc_enable_webp" value="1" lay-skin="switch" lay-text="ON|OFF" {% if
                        options.zkcc_enable_webp=='1' %}checked{% endif %}>
                </div>
            </div>



        </div>
    </div>

</div>
```



## 常见问题

- **Q:** 激活本插件时提示“ZKCC Framework 未激活”？  
  **A:** 请先确保已正确安装并激活 `zkcc-framework` 插件。

- **Q:** 如何扩展后台设置页面？  
  **A:** 请参考 `zkcc-framework` 的文档，使用 `add_filter('zkcc_tabs', ...)` 注册自定义标签页。



## 作者

Zendkee

