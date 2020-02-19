<?php /*a:3:{s:65:"/var/www/caves.wcip.net/application/user/view/user/sign_list.html";i:1568475486;s:57:"/var/www/caves.wcip.net/application/user/view/layout.html";i:1566381559;s:64:"/var/www/caves.wcip.net/application/user/view/public/footer.html";i:1566381559;}*/ ?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="Bookmark" href="/favicon.ico" >
    <link rel="Shortcut Icon" href="/favicon.ico" />
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/lib/html5shiv.js"></script>
    <script type="text/javascript" src="/lib/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="/static/h-ui/css/H-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="/static/h-ui.admin/css/H-ui.admin.css" />
    <link rel="stylesheet" type="text/css" href="/lib/Hui-iconfont/1.0.8/iconfont.css" />
    <link rel="stylesheet" type="text/css" href="/static/h-ui.admin/skin/default/skin.css" id="skin" />
    <link rel="stylesheet" type="text/css" href="/static/h-ui.admin/css/style.css" />
    <!--[if IE 6]>
    <script type="text/javascript" src="/lib/DD_belatedPNG_0.0.8a-min.js" ></script>
    <script>DD_belatedPNG.fix('*');</script>
    <![endif]-->

    <!--_footer 作为公共模版分离出去-->
    <script type="text/javascript" src="/lib/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="/lib/layer/2.4/layer.js"></script>
    <script type="text/javascript" src="/static/h-ui/js/H-ui.min.js"></script>
    <script type="text/javascript" src="/static/h-ui.admin/js/H-ui.admin.js"></script>
    <!--/_footer 作为公共模版分离出去-->
    <title></title>
</head>
<body>
<style>
    .userinfo>td>img {
        width:40px;height:40px;border-radius: 50%;
    }
</style>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 用户管理 <span class="c-gray en">&gt;</span> 用户列表 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
    <section id="formAjax">
        <div class="text-c">
            <button onclick="removeIframe()" class="btn btn-primary radius">关闭选项卡</button>
            <input type="text" name="search" value="<?php echo htmlentities(app('request')->get('search')); ?>" id="search" placeholder=" 用户名称或手机号" style="width:250px" class="input-text">
            <button name="" id="search-btn" class="btn btn-success" type="button"><i class="Hui-iconfont">&#xe665;</i> 查找用户</button>
            <span class="select-box inline" style="width: 120px;">
                <select name="contact" id="contact" class="select">
                    <option value="">全部</option>
                    <option value="0" <?php if($param['contact'] === '0'): ?>selected<?php endif; ?>>未联系</option>
                    <option value="1" <?php if($param['contact'] === '1'): ?>selected<?php endif; ?>>已联系</option>
                </select>
            </span>
        </div>
    </section>
    <div class="cl pd-5 bg-1 bk-gray mt-20">
        <span class="r">共有数据：<strong><?php echo htmlentities($page['count']); ?></strong> 条</span> </div>
    <table class="table table-border table-bordered table-bg">
        <thead>
        <tr class="text-c">
            <th width="35">#</th>
            <th width="60">头像</th>
            <th width="100">昵称</th>
            <th width="100">姓名</th>
            <th width="80">职位</th>
            <th width="120">手机号</th>
            <th width="150">公司名称</th>
            <th width="200">公司地址</th>
            <th width="60">用户状态</th>
            <th width="80">操作</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach($list as $li): ?>
            <tr class="text-c userinfo">
                <td><?php echo htmlentities($li['id']); ?></td>
                <td><img src="<?php if($li['avatar']): if(substr($li['avatar'],0,4) == 'http'): ?><?php echo htmlentities($li['avatar']); else: ?><?php echo htmlentities($qiniu_weburl); ?><?php echo htmlentities($li['avatar']); endif; else: ?>/static/src/image/default.jpg<?php endif; ?>" alt=""></td>
                <td><?php echo htmlentities($li['nickname']); ?></td>
                <td><?php echo htmlentities($li['name']); ?></td>
                <td><?php echo htmlentities($li['job']); ?></td>
                <td><?php echo htmlentities($li['tel']); ?></td>
                <td><?php echo htmlentities($li['company']); ?></td>
                <td><?php echo htmlentities($li['address']); ?></td>
                <td class="td-contact">
                    <?php switch($li['contact']): case "0": ?>
                    <span class="label label-warning radius">未联系</span><?php break; case "1": ?>
                    <span class="label label-success radius">已联系</span><?php break; default: endswitch; ?>
                </td>
                <td class="td-manage">
                    <?php if($li['contact'] == 0): ?>
                    <a style="text-decoration:none" class="ml-5" onClick="contact(this,<?php echo htmlentities($li['id']); ?>)" href="javascript:;" title="联系">
                        <i class="Hui-iconfont">&#xe6a3;</i>
                    </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>

            <tr>
                <td colspan="12" id="page" class="text-r"></td>
            </tr>
        </tbody>
    </table>
</div>
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/lib/My97DatePicker/4.8/WdatePicker.js"></script>
<script type="text/javascript" src="/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/lib/laypage/1.2/laypage.js"></script>
<script type="text/javascript">

    var url = '<?php echo url("User/signList"); ?>' + '?<?php echo $page["query"];?>';
    var curr = '<?php echo htmlentities($page['curr']); ?>',totalPage='<?php echo htmlentities($page['totalPage']); ?>';
    if(totalPage > 1) {
        laypage({
            cont: 'page', //容器。值支持id名、原生dom对象，jquery对象。
            pages: '<?php echo htmlentities($page['totalPage']); ?>', //通过后台拿到的总页数
            skip: true, //是否开启跳页
            skin: '#5a98de',
            curr: curr || 1, //当前页
            jump: function(obj, first){ //触发分页后的回调
                // console.log(obj)
                if(!first) {
                    window.location.href = url+'&page=' + obj.curr;
                }
            }
        });
    }

    $("#search-btn").click(function () {
        var search = $("#formAjax").find("#search").val();
        var contact = $("#formAjax").find("#contact").val();
        var str = '';
        if(search.length != '') {
            str += '&search=' + search
        }
        if(contact.length != '') {
            str += '&contact=' + contact
        }
        window.location.href = '<?php echo url("User/signList"); ?>' + '?' + str;
    });

    function contact(obj,id) {
        layer.confirm('确认已联系过吗？',function(index){
            $.ajax({
                url: "<?php echo url('User/signContact'); ?>",
                type: 'POST',
                dataType: 'json',
                data:{id:id},
                success: function(data){
                    console.log(data);
                    if(data.code == 1) {
                        $(obj).parents('tr').find('.td-contact').html('<span class="label label-success radius">已联系</span>');
                        $(obj).remove();
                        layer.msg('已联系!',{icon:1,time:1000});
                    }else {
                        layer.msg('操作失败!',{icon:2,time:1000});
                    }
                },
                error:function(data) {
                    console.log(data.msg);
                }
            });

        });

    }




</script>
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/lib/jquery.contextmenu/jquery.contextmenu.r2.js"></script>
<script type="text/javascript">
    /*个人信息*/
    function myselfinfo(){
        layer.open({
            type: 1,
            area: ['300px','200px'],
            fix: false, //不固定
            maxmin: true,
            shade:0.4,
            title: '查看信息',
            content: '<div>管理员信息</div>'
        });
    }


</script>
</body>
</html>
