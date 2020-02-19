<?php /*a:3:{s:71:"/var/www/caves.wcip.net/application/admin/view/banner/slide_detail.html";i:1569806140;s:58:"/var/www/caves.wcip.net/application/admin/view/layout.html";i:1566381559;s:65:"/var/www/caves.wcip.net/application/admin/view/public/footer.html";i:1566381559;}*/ ?>
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
    .thumbnail{ width:400px}
</style>
<div class="page-container">
    <form class="form form-horizontal" id="formAjax">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>图片描述：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="<?php echo htmlentities($info['title']); ?>" placeholder="" name="title">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>链接地址：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="<?php echo htmlentities($info['url']); ?>" placeholder="" name="url">
                <input type="hidden" name="pic" id="pic" value="<?php echo htmlentities($info['pic']); ?>">
            </div>
        </div>
        <div class="row cl" id="picsize">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span></label>
            <div class="formControls col-xs-8 col-sm-9">
                <span style="font-size: 16px">
                    (建议尺寸: 702*350)
                </span>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">
                <span id="btn-star" class="btn btn-primary uploadfile radius ml-10">更换图片</span>
            </label>
            <div class="formControls col-xs-8 col-sm-9">
                <div class="uploader-thum-container">
                    <div id="fileList" class="uploader-list">
                        <?php if($info['pic']): ?>
                        <img class="thumbnail" id="pic_url" src="<?php echo config('qiniu_weburl');?><?php echo htmlentities($info['pic']); ?>" />
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                <button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe600;</i> 保存</button>
                <button onClick="layer_close();" class="btn btn-default radius" type="button">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
            </div>
        </div>
        <input type="hidden" name="id" class="input-text" value="<?php echo htmlentities($info['id']); ?>" placeholder="">
    </form>
    <input type="file" id="qiniu-file" name="file" style="display: none">
</div>

<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/jquery.validate.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/messages_zh.js"></script>
<script type="text/javascript" src="/lib/qiniu.min.js"></script>
<script type="text/javascript">

    $(function() {
        var isclick = true;

        $("#formAjax").validate({
            rules:{
                title:{
                    required:true,
                    maxlength:100
                },
            },
            onkeyup:false,
            focusCleanup:true,
            success:"valid",
            submitHandler:function(form){
                if(isclick === true) {
                    isclick = false;
                    $(form).ajaxSubmit({
                        type: 'post',
                        url: "<?php echo url('Banner/slideMod'); ?>" ,
                        success: function(data){
                            console.log(data);
                            // isclick = true
                            if(data.code == 1) {
                                layer.msg('修改成功!',{icon:1,time:1000},function () {
                                    window.parent.location.reload();
                                });
                            }else {
                                layer.msg(data.data,{icon:2,time:1000});
                                isclick = true
                            }
                        },
                        error: function(XmlHttpRequest, textStatus, errorThrown){
                            layer.msg('接口请求错误!',{icon:5,time:1000});
                            isclick = true
                        }
                    });
                }
            }
        });

        $(".uploadfile").click(function () {
            $("#qiniu-file").click();
        });

        $(document).on("change","#qiniu-file",function(){
            var obj = $("#qiniu-file");
            var fileName = obj.val();//上传的本地文件绝对路径
            $("#uploadfile").val(fileName);
            if(fileName === '') {
                return;
            }else {
                console.log(fileName,' file.value');
            }

            var suffix = fileName.substring(fileName.lastIndexOf("."),fileName.length);//后缀名
            var suffix_str = suffix.toUpperCase();
            if(suffix_str!=".BMP"&&suffix_str!=".JPG"&&suffix_str!=".JPEG"&&suffix_str!=".PNG"&&suffix_str!=".GIF"){
                alert("请上传图片（格式BMP、JPG、JPEG、PNG、GIF等）!");
                return;
            }
            var file = obj.get(0).files[0];	                                           //上传的文件
            if(file.size > 128*1024) {
                layer.alert('上传文件大小不超过128Kb');
                return;
            }
            //七牛云上传
            $.ajax({
                type:'post',
                url: "<?php echo url('Qiniu/getUpToken'); ?>",
                data:{"suffix":suffix},
                dataType:'json',
                success: function(result){
                    if(result.code == 1){
                        var observer = {                         //设置上传过程的监听函数
                            next(res){                        //上传中(result参数带有total字段的 object，包含loaded、total、percent三个属性)
                                Math.floor(res.total.percent);//查看进度[loaded:已上传大小(字节);total:本次上传总大小;percent:当前上传进度(0-100)]
                                console.log(Math.floor(res.total.percent));
                            },
                            error(err){                          //失败后
                                alert(err.message);
                            },
                            complete(res){                       //成功后
                                // console.log(res,'---upload success');
                                layer.alert('上传成功',{icon:6});
                                $("#pic").val(result.data.filename);
                                $("#pic_url").attr('src',result.data.weburl + result.data.filename);
                            }
                        };
                        var putExtra = {
                            fname: "",                          //原文件名
                            params: {},                         //用来放置自定义变量
                            mimeType: null                      //限制上传文件类型
                        };
                        var config = {
                            region:qiniu.region.z1,             //存储区域(z0:代表华东;z2:代表华南,不写默认自动识别)
                            concurrentRequestLimit:3            //分片上传的并发请求量
                        };
                        var observable = qiniu.upload(file,result.data.filename,result.data.token,putExtra,config);
                        var subscription = observable.subscribe(observer);          // 上传开始
                        // 取消上传
                        // subscription.unsubscribe();
                    }else{
                        alert(result.data);                  //获取凭证失败
                    }
                },error:function(){                             //服务器响应失败处理函数
                    layer.alert("服务器繁忙");
                }
            });
        });


    })

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
