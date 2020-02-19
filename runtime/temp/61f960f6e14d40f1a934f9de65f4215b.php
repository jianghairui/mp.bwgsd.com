<?php /*a:3:{s:68:"/var/www/caves.wcip.net/application/admin/view/fake/role_detail.html";i:1576568420;s:58:"/var/www/caves.wcip.net/application/admin/view/layout.html";i:1566381559;s:65:"/var/www/caves.wcip.net/application/admin/view/public/footer.html";i:1566381559;}*/ ?>
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
    .thumbnail{ width:300px;}
</style>
<article class="page-container">
    <form class="form form-horizontal" id="formAjax">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">角色：</label>
            <div class="formControls col-xs-4 col-sm-4">
                <span class="select-box">
                <select class="select" name="role" id="role">
                    <option value="3" <?php if($info['role'] == '3'): ?>selected<?php endif; ?>>工厂</option>
                    <option value="1" <?php if($info['role'] == '1'): ?>selected<?php endif; ?>>博物馆</option>
                    <option value="2" <?php if($info['role'] == '2'): ?>selected<?php endif; ?>>设计师</option>
				</select>
                </span>
            </div>
            <div class="col-3">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span></label>
            <div class="formControls col-xs-8 col-sm-9">
                <span style="font-size: 16px">
                    (建议尺寸: 132 * 132)
                </span>
            </div>
        </div>
        <div class="row cl" id="pic">
            <label class="form-label col-xs-4 col-sm-2">
                <span class="btn btn-primary uploadfile radius ml-10">角色头像</span>
            </label>
            <div class="formControls col-xs-8 col-sm-9">
                <div class="uploader-thum-container">
                    <div id="fileList" class="uploader-list">
                        <img class="thumbnail" src="<?php echo htmlentities($qiniu_weburl); ?><?php echo htmlentities($info['avatar']); ?>" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span></label>
            <div class="formControls col-xs-8 col-sm-9">
                <span style="font-size: 16px">
                    (建议尺寸: 702 * 300)
                </span>
            </div>
        </div>
        <div class="row cl" id="pic2">
            <label class="form-label col-xs-4 col-sm-2">
                <span class="btn btn-primary uploadfile2 radius ml-10">角色封面</span>
            </label>
            <div class="formControls col-xs-8 col-sm-9">
                <div class="uploader-thum-container">
                    <div id="fileList2" class="uploader-list">
                        <img class="thumbnail" src="<?php echo htmlentities($qiniu_weburl); ?><?php echo htmlentities($info['cover']); ?>" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>用户昵称：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="<?php echo htmlentities($info['nickname']); ?>" placeholder="用户昵称" id="nickname" name="nickname" maxlength="50">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>手机号：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="<?php echo htmlentities($info['tel']); ?>" placeholder="手机号" id="tel" name="tel" maxlength="15">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>ORG(组织机构名称)：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="<?php echo htmlentities($info['org']); ?>" placeholder="组织机构名称" id="org" name="org" maxlength="30">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">简介|经营范围：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <textarea name="desc" cols="" rows="" class="textarea" maxlength="255" placeholder="" oninput="this.value.length<=255?$('#textlen1').text(this.value.length):$('#textlen1').text(255)" ><?php echo htmlentities($info['desc']); ?></textarea>
                <p class="textarea-numberbar"><em class="textarea-length" id="textlen1">0</em>/255</p>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">选择地区：</label>
            <div class="formControls col-xs-2 col-sm-2" > <span class="select-box">
				<select class="select" name="provinceCode" id="provinceCode">
                    <option value="0">省</option>
                    <?php foreach($province_list as $li): ?>
					<option value="<?php echo htmlentities($li['code']); ?>" <?php if($li['code'] == $info['province_code']): ?>selected<?php endif; ?>><?php echo htmlentities($li['name']); ?></option>
                    <?php endforeach; ?>
				</select>
				</span>
            </div>
            <div class="formControls col-xs-2 col-sm-2"> <span class="select-box">
				<select class="select" name="cityCode" id="cityCode">
                    <option value="0">市</option>
                    <?php foreach($city_list as $li): ?>
					<option value="<?php echo htmlentities($li['code']); ?>" <?php if($li['code'] == $info['city_code']): ?>selected<?php endif; ?>><?php echo htmlentities($li['name']); ?></option>
                    <?php endforeach; ?>
				</select>
				</span>
            </div>
            <div class="formControls col-xs-2 col-sm-2"> <span class="select-box">
				<select class="select" name="regionCode" id="regionCode">
                    <option value="0">区</option>
                    <?php foreach($region_list as $li): ?>
					<option value="<?php echo htmlentities($li['code']); ?>" <?php if($li['code'] == $info['region_code']): ?>selected<?php endif; ?>><?php echo htmlentities($li['name']); ?></option>
                    <?php endforeach; ?>
				</select>
				</span>
            </div>
        </div>
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                <button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存</button>
                <button onClick="layer_close();" class="btn btn-default radius" type="button">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
            </div>
            <input type="hidden" name="avatar" id="avatar" value="<?php echo htmlentities($info['avatar']); ?>">
            <input type="hidden" name="cover" id="cover" value="<?php echo htmlentities($info['cover']); ?>">
            <input type="hidden" name="id" value="<?php echo htmlentities($info['id']); ?>">
        </div>
    </form>
    <input type="file" name="file" id="qiniu-file" style="display: none;">
    <input type="file" name="file" id="qiniu-file2" style="display: none;">
</article>

<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/lib/My97DatePicker/4.8/WdatePicker.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/jquery.validate.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/messages_zh.js"></script>

<script type="text/javascript" src="/lib/ueditor/1.4.3/ueditor.config.js"></script>
<script type="text/javascript" src="/lib/ueditor/1.4.3/ueditor.all.min.js"> </script>
<script type="text/javascript" src="/lib/ueditor/1.4.3/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript" src="/lib/qiniu.min.js"></script>
<script type="text/javascript">

    $(function(){

        var isclick = true;

        $(".portfolio-area li").Huihover();

        //表单验证
        $("#formAjax").validate({
            rules:{},
            focusCleanup:false,
            success:"valid",
            submitHandler:function(form){
                if(isclick === true) {
                    isclick = false;
                    $(form).ajaxSubmit({
                        type: 'post',
                        url: "<?php echo url('Fake/roleMod'); ?>" ,
                        success: function(data){
                            // console.log(data);return;
                            if(data.code == 1) {
                                layer.msg('保存成功!',{icon:1,time:1000},function () {
                                    window.parent.location.reload();
                                });
                            }else {
                                layer.msg(data.data,{icon:2,time:1000});
                                isclick = true
                            }

                        },
                        error: function(XmlHttpRequest, textStatus, errorThrown){
                            layer.msg('接口请求失败!',{icon:5,time:1000});
                            isclick = true
                        }
                    });
                }

            }
        });


        $(".uploadfile").click(function () {
            $("#qiniu-file").click();
        });

        $(document).on("change","#qiniu-file",function() {
            var load = null;
            var obj = $("#qiniu-file");
            var fileName = obj.val();//上传的本地文件绝对路径
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
            var limitsize = 128;
            if(file.size > limitsize*1024) {
                layer.alert('上传文件大小不超过'+limitsize+'Kb');
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
                        load = layer.load(2);
                        var observer = {                         //设置上传过程的监听函数
                            next(res){                        //上传中(result参数带有total字段的 object，包含loaded、total、percent三个属性)
                                Math.floor(res.total.percent);//查看进度[loaded:已上传大小(字节);total:本次上传总大小;percent:当前上传进度(0-100)]
                                // console.log(Math.floor(res.total.percent));
                            },
                            error(err){                          //失败后
                                alert(err.message);
                            },
                            complete(res){                       //成功后
                                // console.log(res,'---upload success');
                                $("#avatar").val(result.data.filename);
                                $("#fileList").html('<img class="thumbnail" src="' + result.data.weburl + result.data.filename + '" />')
                                layer.close(load);
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

        $(".uploadfile2").click(function () {
            $("#qiniu-file2").click();
        });

        $(document).on("change","#qiniu-file2",function() {
            var load = null;
            var obj = $("#qiniu-file2");
            var fileName = obj.val();//上传的本地文件绝对路径
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
            var limitsize = 128;
            if(file.size > limitsize*1024) {
                layer.alert('上传文件大小不超过'+limitsize+'Kb');
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
                        load = layer.load(2);
                        var observer = {                         //设置上传过程的监听函数
                            next(res){                        //上传中(result参数带有total字段的 object，包含loaded、total、percent三个属性)
                                Math.floor(res.total.percent);//查看进度[loaded:已上传大小(字节);total:本次上传总大小;percent:当前上传进度(0-100)]
                                // console.log(Math.floor(res.total.percent));
                            },
                            error(err){                          //失败后
                                alert(err.message);
                            },
                            complete(res){                       //成功后
                                // console.log(res,'---upload success');
                                $("#cover").val(result.data.filename);
                                $("#fileList2").html('<img class="thumbnail" src="' + result.data.weburl + result.data.filename + '" />')
                                layer.close(load);
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



        $("#provinceCode").change(function () {
            var provinceCode = $(this).val();
            if(provinceCode == '0') {
                $("#cityCode").html('<option value="0">市</option>');
                $("#regionCode").html('<option value="0">区</option>');
                return;
            }else {
                if(isclick) {
                    isclick = false;
                    getCityList(provinceCode);
                }
            }

        });

        $("#cityCode").change(function () {
            var cityCode = $(this).val();
            if(cityCode == '0') {
                $("#regionCode").html('<option value="0">区</option>');
                return;
            }else {
                if(isclick) {
                    isclick = false;
                    getRegionList(cityCode);
                }
            }
        });

        function getCityList(provinceCode) {
            $.ajax({
                url: "<?php echo url('User/getCityList'); ?>" ,
                type: 'post',
                data:{provinceCode:provinceCode},
                success: function(data){
                    if(data.code == 1) {
                        var html = '<option value="0">市</option>',list = data.data;
                        if(list.length > 0) {
                            for(var i=0; i <list.length;i++) {
                                html += '<option value="'+list[i].code+'">'+list[i].name+'</option>';
                            }
                        }
                        $("#cityCode").html(html);
                        if(list.length > 0) {
                            getRegionList(list[0].code);
                        }
                    }else {
                        layer.msg('接口异常',{icon:5,time:1000});
                    }
                    isclick = true;
                    // console.log(data);
                },
                error: function(res){
                    layer.msg('接口请求失败!',{icon:5,time:1000});
                    isclick = true;

                }
            })
        }

        function getRegionList(cityCode) {
            $.ajax({
                url: "<?php echo url('User/getRegionList'); ?>" ,
                type: 'post',
                data:{cityCode:cityCode},
                success: function(data){
                    if(data.code == 1) {
                        var html = '<option value="0">区</option>',list = data.data;
                        if(list.length > 0) {
                            for(var i=0; i <list.length;i++) {
                                html += '<option value="'+list[i].code+'">'+list[i].name+'</option>';
                            }
                        }
                        $("#regionCode").html(html);
                    }else {
                        layer.msg('接口异常',{icon:5,time:1000});
                    }
                    isclick = true;
                    // console.log(data);
                },
                error: function(res){
                    layer.msg('接口请求失败!',{icon:5,time:1000});
                    isclick = true;

                }
            })
        }

        var textlen1 = $("textarea[name='desc']").val().length;
        $("#textlen1").text(textlen1);


    });





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
