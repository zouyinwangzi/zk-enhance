
layui.use(['laydate', 'laypage', 'layer', 'table', 'carousel', 'upload', 'element'], function () {
    // var laypage = layui.laypage ;//分页
    var layer = layui.layer;//弹层
    var table = layui.table;//表格
    var form = layui.form;//表单
    var $ = layui.jquery;


    //监听misc的禁用图像尺寸 全选/全不选
    layui.use(['form', 'jquery'], function () {
        var form = layui.form;
        var $ = layui.jquery;
        //点击全选, 勾选
        form.on('switch(image_size_select_all)', function (data) {
            var child = $(".disable_image_size input[type='checkbox']");
            child.each(function (index, item) {
                item.checked = data.elem.checked;
            });
            form.render('checkbox');
        });
    });

});