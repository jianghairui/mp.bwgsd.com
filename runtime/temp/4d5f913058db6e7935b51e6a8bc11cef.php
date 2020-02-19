<?php /*a:1:{s:58:"/var/www/caves.wcip.net/application/api/view/yu/index.html";i:1577952962;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <link rel="stylesheet" href="/nanduhu/styles/common.css">
  <link rel="stylesheet" href="/nanduhu/styles/jquery-weui.min.css">
  <link rel="stylesheet" href="/nanduhu/swiper/swiper.min.css">
  <link rel="stylesheet" href="/nanduhu/styles/index.css">

  <title>领取提货</title>
</head>
<body>
<div class="main">
  <div class="swiper-container swiper">
    <div class="swiper-wrapper">
      <div class="swiper-slide" style="background-image: url('/nanduhu/images/banner1.png')"></div>
      <div class="swiper-slide" style="background-image: url('/nanduhu/images/banner2.png')"></div>
    </div>
    <div class="swiper-pagination"></div>
  </div>

  <div class="title">
    <div class="line"></div>
    <h3>礼品券卡信息</h3>
  </div>

  <div class="ipt-box">
    <div class="input">
      <input type="text" id="card_no" name="card_no" placeholder="序列号" value="" max="10">
    </div>
    <div class="input">
      <input type="text" id="card_key" name="card_key" placeholder="提货码" value="" max="10">
    </div>
  </div>
  <div class="title">
    <div class="line"></div>
    <h3>收货信息</h3>
  </div>
  <div class="ipt-box">
    <div class="input">
      <input type="text" id="tel" name="tel" placeholder="手机号" value="" max="11">
    </div>
    <div class="small-code">
      <div class="input">
        <input type="text" id="code" name="code" placeholder="短信验证码" value="" maxlength="6">
      </div>
      <div class="get-code" id="get_code">获取验证码</div>
    </div>
    <div class="input">
      <input type="text" id="receiver" name="receiver" placeholder="收货人姓名" max="20">
    </div>
    <div class="choice-address-box">
      <div class="input">
        <input type="text" id="city-picker" placeholder="点击选择省市区" readonly>
        <!--<input type="text" readonly id="choice_address" placeholder="点击选择省市区">-->
      </div>
      <div class="next-icon"><img src="/nanduhu/images/icon_jiantou.png"/></div>
    </div>
    <div class="input">
      <input type="text" id="address" placeholder="详细地址" max="100">
    </div>
  </div>
  <div class="btn-box">
    <div class="btn-receive" id="btn_receive">点击领取</div>
    <a href="<?php echo url('Yu/recordList'); ?>">查看领取记录</a>
  </div>
</div>
</body>
<script src="/nanduhu/javascript/jquery-3.4.1.min.js"></script>
<script src="/nanduhu/layer/mobile/layer.js"></script>
<script src="/nanduhu/swiper/swiper.min.js"></script>
<script src="/nanduhu/javascript/jquery-weui.min.js"></script>
<script src="/nanduhu/javascript/city-picker.min.js"></script>
<script>

  $(function () {
      var tel_rule = /^1\d{10}$/;
      var province = '';
      var city = '';
      var region = '';
      var swiper = new Swiper('.swiper', {
          pagination: {
              el: '.swiper-pagination',
          },
          autoplay: {
              delay: 3000,
              stopOnLastSlide: false,
              disableOnInteraction: true,
          },
      });

      var click_lock = true,sms_click_lock = true;
      var countDownT = 60;

      $('#btn_receive').click(function () {
          var card_no = $("#card_no").val();
          var card_key = $("#card_key").val();
          var tel = $("#tel").val();
          var code = $("#code").val();
          var receiver = $("#receiver").val();
          var address = $("#address").val();

          if(click_lock) {
              click_lock = false;
              if(!card_no) {
                  layer.open({content: '序列号不能为空', skin: 'msg', time: 1,end() { click_lock = true;}});
                  return;
              }
              if(!card_key) {
                  layer.open({content: '提货码不能为空', skin: 'msg', time: 1,end() { click_lock = true;}});
                  return;
              }
              if(!tel) {
                  layer.open({content: '手机号不能为空', skin: 'msg', time: 1,end() { click_lock = true;}});
                  return;
              }
              if(!tel_rule.test(tel)) {
                  layer.open({content: '无效的手机号', skin: 'msg', time: 1,end() { click_lock = true;}});
                  return;
              }
              if(!code) {
                  layer.open({content: '验证码不能为空', skin: 'msg', time: 1,end() { click_lock = true;}});
                  return;
              }
              if(!receiver || !address || !province || !city || !region) {
                  layer.open({content: '请完善收货信息', skin: 'msg', time: 1,end() { click_lock = true;}});
                  return;
              }

              $.ajax({
                  url:'<?php echo url("Yu/pickupGoods"); ?>',
                  type:'post',
                  dataType:'json',
                  data:{
                      card_no:card_no,
                      card_key:card_key,
                      tel:tel,
                      code:code,
                      receiver:receiver,
                      province:province,
                      city:city,
                      region:region,
                      address:address
                  },
                  success:function(data) {
                      if(data.code == 1) {
                          layer.open({
                              content: '领取成功，前往查看领取记录',
                              btn: ['前往', '取消'],
                              yes(index) {
                                  location.href = '<?php echo url("Yu/recordList"); ?>'
                              }
                          });
                      }else {
                          layer.open({content: data.data, skin: 'msg', time: 1,end() { click_lock = true;}});
                      }
                  },
                  error:function(e) {
                      layer.open({content: '网络异常!', skin: 'msg', time: 1,end() { click_lock = true;}});
                  }
              });
          }
          //提示
      });


      $('#get_code').click(function () {
          var tel = $("#tel").val();
          if(sms_click_lock) {
              sms_click_lock = false;
              if(!tel) {
                  layer.open({content: '手机号不能为空', skin: 'msg', time: 1,end() { sms_click_lock = true;}});
                  return;
              }
              if(!tel_rule.test(tel)) {
                  layer.open({content: '无效的手机号', skin: 'msg', time: 1,end() { sms_click_lock = true;}});
                  return;
              }

              $.ajax({
                  url:'<?php echo url("Yu/sendSms"); ?>',
                  type:'post',
                  dataType:'json',
                  data:{
                      tel:tel
                  },
                  success:function(data) {
                      if(data.code == 1) {
                          layer.open({
                              content: '验证码发送成功',
                              skin: 'msg',
                              time: 2,
                              success: function () {
                                  setTime();
                              }
                          });
                      }else {
                          layer.open({
                              content: data.data,
                              skin: 'msg',
                              time: 2
                          });
                          sms_click_lock = true;
                      }

                  },
                  error:function(e) {
                      layer.open({
                          content: '网络异常!',
                          skin: 'msg',
                          time: 2
                      });
                      sms_click_lock = true;
                  }
              });
          }

      });

      function setTime(){
          if (countDownT == 0){
              click_lock = true;
              countDownT = 60;
              $("#get_code").text("获取短信验证码");
          } else{
              $("#get_code").text("重新发送("+countDownT+"s)");
              countDownT--;
              setTimeout(function () {
                  setTime();
              },1000)
          }
      }

      $("#city-picker").val('');

      $("#city-picker").cityPicker({
        title: "选择省市区/县",
        onChange: function (picker, values, displayValues) {
            province = displayValues[0];
            city = displayValues[1];
            region = displayValues[2];
            //console.log(values, displayValues);
        }
      });




  });
</script>
</html>
