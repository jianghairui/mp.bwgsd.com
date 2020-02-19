<?php /*a:3:{s:66:"/var/www/caves.wcip.net/application/admin/view/yu/record_list.html";i:1577955566;s:58:"/var/www/caves.wcip.net/application/admin/view/layout.html";i:1566381559;s:65:"/var/www/caves.wcip.net/application/admin/view/public/footer.html";i:1566381559;}*/ ?>
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
    .avatar{ width:44px;height: 44px;border-radius: 50%;}
</style>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 南都湖鱼 <span class="c-gray en">&gt;</span> 领取记录 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
    <section id="formAjax">
        <div class="text-c">
            <button onclick="removeIframe()" class="btn btn-primary radius">关闭选项卡</button>
            日期范围：
            <input type="text" onfocus="WdatePicker({ maxDate:'#F{ $dp.$D(\'datemax\')||\'%y-%M-%d\' }' })" id="datemin" value="<?php echo htmlentities(app('request')->get('datemin')); ?>" class="input-text Wdate" style="width:120px;">
            -
            <input type="text" onfocus="WdatePicker({ minDate:'#F{ $dp.$D(\'datemin\') }' })" id="datemax" value="<?php echo htmlentities(app('request')->get('datemax')); ?>" class="input-text Wdate" style="width:120px;">
            <span class="select-box inline" style="width: 120px;">
                <select name="status" id="status" class="select">
                    <option value="">全部</option>
                    <option value="0" <?php if($param['status'] === '0'): ?>selected<?php endif; ?>>未使用</option>
                    <option value="1" <?php if($param['status'] === '1'): ?>selected<?php endif; ?>>已使用</option>
                </select>
            </span>
            <span class="select-box inline" style="width: 120px;">
                <select name="send" id="send" class="select">
                    <option value="">全部</option>
                    <option value="0" <?php if($param['send'] === '0'): ?>selected<?php endif; ?>>未发货</option>
                    <option value="1" <?php if($param['send'] === '1'): ?>selected<?php endif; ?>>已发货</option>
                </select>
            </span>

            <input type="text" name="search" value="<?php echo htmlentities($param['search']); ?>" id="search" placeholder=" 序列号或收货人姓名" style="width:200px" class="input-text">
            <button name="" id="search-btn" class="btn btn-success" type="button"><i class="Hui-iconfont">&#xe665;</i> 查找</button>
            <button name="" id="toExcel" class="btn btn-primary" type="button"><i class="Hui-iconfont">&#xe665;</i> 导出Excel</button>
        </div>
    </section>
    <div class="cl pd-5 bg-1 bk-gray mt-20">
        <span class="r">共有数据：<strong><?php echo htmlentities($page['count']); ?></strong> 条</span> </div>
    <table class="table table-border table-bordered table-bg">
        <thead>
        <tr class="text-c">
            <th width="30">#</th>
            <th width="100">序列号</th>
            <th width="60">使用状态</th>
            <th width="60">发货状态</th>
            <th width="80">快递类型</th>
            <th width="150">物流单号</th>
            <th width="150">领取时间</th>
            <th>领取人昵称</th>
            <th width="44">头像</th>
            <th width="80">操作</th>
        </tr>
        </thead>
        <tbody>
        <form id="multi-check">
            <?php foreach($list as $li): ?>
            <tr class="text-c userinfo">
                <td rowspan="2"><?php echo htmlentities($li['id']); ?></td>
                <td><b><?php echo htmlentities($li['card_no']); ?></b></td>
                <td>
                    <?php switch($li['status']): case "0": ?>
                    <span class="label label-warning radius">未使用</span>
                    <?php break; case "1": ?>
                    <span class="label label-success radius">已使用</span>
                    <?php break; default: endswitch; ?>
                </td>
                <td>
                    <?php switch($li['send']): case "0": ?>
                    <span class="label label-defaunt radius">待发货</span>
                    <?php break; case "1": ?>
                    <span class="label label-success radius">已发货</span>
                    <?php break; default: endswitch; ?>
                </td>
                <td><?php echo htmlentities($li['tracking_name']); ?></td>
                <td><?php echo htmlentities($li['tracking_no']); ?></td>
                <td><?php echo htmlentities($li['take_time']); ?></td>
                <td><?php echo htmlentities($li['nickname']); ?></td>
                <td>
                    <img src="<?php echo htmlentities($li['avatar']); ?>" class="avatar" />
                </td>
                <td class="td-manage" rowspan="2">
                    <span id="send-btn">
                        <?php if($li['status'] == 1 && $li['send'] == 0): ?>
                        <a title="发货" href="javascript:;" onclick="order_send('订单发货','<?php echo url("Yu/orderSend",array("id"=>$li["id"])); ?>','600','400')"">
                            <i class="Hui-iconfont">&#xe669;</i>
                        </a>
                        <?php endif; ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="8">
                    <span style="margin-left: 15px;">收货地址: <b><?php echo htmlentities($li['province']); ?> <?php echo htmlentities($li['city']); ?> <?php echo htmlentities($li['region']); ?> <?php echo htmlentities($li['address']); ?></b></span> &nbsp;&nbsp;&nbsp;&nbsp;(收货人: <b><?php echo htmlentities($li['receiver']); ?></b> | 手机号: <b><?php echo htmlentities($li['tel']); ?></b> )
                </td>
            </tr>
            <?php endforeach; ?>
        </form>

        <tr>
            <td colspan="10" id="page" class="text-r"></td>
        </tr>
        </tbody>
    </table>
</div>
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/lib/My97DatePicker/4.8/WdatePicker.js"></script>
<script type="text/javascript" src="/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/lib/laypage/1.2/laypage.js"></script>
<script type="text/javascript">

    var url = '<?php echo url("Yu/recordList"); ?>' + '?<?php echo $page["query"];?>';
    var curr = '<?php echo htmlentities($page['curr']); ?>',totalPage='<?php echo htmlentities($page['totalPage']); ?>';
    if(totalPage > 1) {
        laypage({
            cont: 'page', //容器。值支持id名、原生dom对象，jquery对象。
            pages: totalPage, //通过后台拿到的总页数
            skip: false, //是否开启跳页
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
        var datemin = $("#formAjax").find("#datemin").val();
        var datemax = $("#formAjax").find("#datemax").val();
        var search = $("#formAjax").find("#search").val();
        var status = $("#formAjax").find("#status").val();
        var send = $("#formAjax").find("#send").val();
        var str = '';
        if(datemin.length != '') {
            str += '&datemin=' + datemin
        }
        if(datemax.length != '') {
            str += '&datemax=' + datemax
        }
        if(search.length != '') {
            str += '&search=' + search
        }
        if(status.length != '') {
            str += '&status=' + status
        }
        if(send.length != '') {
            str += '&send=' + send
        }
        window.location.href = '<?php echo url("Yu/recordList"); ?>' + '?' + str;
    });

    $("#toExcel").click(function () {

        var datemin = $("#formAjax").find("#datemin").val();
        var datemax = $("#formAjax").find("#datemax").val();
        var search = $("#formAjax").find("#search").val();
        var status = $("#formAjax").find("#status").val();
        var send = $("#formAjax").find("#send").val();
        var str = '';
        if(datemin.length != '') {
            str += '&datemin=' + datemin
        }
        if(datemax.length != '') {
            str += '&datemax=' + datemax
        }
        if(search.length != '') {
            str += '&search=' + search
        }
        if(status.length != '') {
            str += '&status=' + status
        }
        if(send.length != '') {
            str += '&send=' + send
        }
        window.location.href = '<?php echo url("Yu/toExcel"); ?>' + '?' + str;
    });

    /*发货*/
    function order_send(title,url,w,h){
        layer_show(title,url,w,h);
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
