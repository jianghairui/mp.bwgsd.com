<?php /*a:1:{s:64:"/var/www/caves.wcip.net/application/api/view/yu/record_list.html";i:1577955477;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="/nanduhu/styles/common.css">
  <link rel="stylesheet" href="/nanduhu/styles/record.css">

  <title>领取记录</title>
</head>
<body>
<div class="main">
    <?php if(empty($list)): ?>
    <div class="tip">暂无领取记录</div>
    <?php endif; ?>
    <ul class="list-box">
    <?php foreach($list as $li): ?>
    <li>
      <h3><?php echo htmlentities($li['take_time']); ?></h3>
      <div class="item-box">
        <div class="item-left">
          <p>序列号</p>
          <p>收货人</p>
          <p>手机号</p>
          <p>收货地址</p>
        </div>
        <div class="item-right">
          <p><?php echo htmlentities($li['card_no']); ?></p>
          <p><?php echo htmlentities($li['receiver']); ?></p>
          <p><?php echo htmlentities($li['tel']); ?></p>
          <p><?php echo htmlentities($li['province']); ?> <?php echo htmlentities($li['city']); ?> <?php echo htmlentities($li['region']); ?> <?php echo htmlentities($li['address']); ?></p>
        </div>
      </div>
      <div class="item-box item-bottom-box">
          <?php switch($li['send']): case "0": ?>
            <div class="item-left">
              <p>配送状态</p>
            </div>
            <div class="item-right">
              <p>未发货</p>
            </div>
          <?php break; case "1": ?>
          <div class="item-left">
            <p>配送状态</p>
            <p>物流单号</p>
          </div>
          <div class="item-right">
              <p>已发货</p>
              <p class="wuliu">
                <span><?php echo htmlentities($li['tracking_no']); ?>(<?php echo htmlentities($li['tracking_name']); ?>)</span>
                <span class="btn-copy" onclick="copy_data('<?php echo htmlentities($li['tracking_no']); ?>')">复制</span>
              </p>
          </div>
          <?php break; default: endswitch; ?>
      </div>

    </li>
    <?php endforeach; ?>
  </ul>
</div>
</body>
<script src="/nanduhu/javascript/jquery-3.4.1.min.js"></script>
<script src="/nanduhu/layer/mobile/layer.js"></script>
<script>
  $(function () {
    // $('.btn-copy').click(function () {
    //   layer.open({
    //     content: '复制成功',
    //     skin: 'msg',
    //     time: 2
    //   })
    // });
  });

  function copy_data(msg) {
      var input = document.createElement("input");
      input.value = msg;
      input.readOnly = true;
      document.body.appendChild(input);
      input.select();
      input.setSelectionRange(0, input.value.length);
      document.execCommand('Copy');
      document.body.removeChild(input);
      window.scrollTo(0, 0);
      layer.open({
        content: '复制成功',
        skin: 'msg',
        time: 2
      })
  }
</script>
</html>
