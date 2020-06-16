<?php
namespace app\api\controller;
use think\Db;
use my\Sendsms;

class Notifysms extends Base {

//    public function index() {
//        $this->asyn_sms_send([]);
//        echo 'SUCCESS';
//    }

    //通知商家
    public function goodsOrder() {
        $order_id = input('param.orderid');
        $sms = new Sendsms();
        $tel = config('notify_tel');

        $sms_data['tpl_code'] = 'SMS_193247578';
        $sms_data['tel'] = $tel;
        $sms_data['param'] = [
            'status' => '已支付',
            'orderid' => $order_id
        ];
        $res = $sms->send($sms_data);
        if($res->Code === 'OK') {
            return ajax();
        }else {
            $this->msglog($this->cmd,$res->Message);
            return ajax($res->Message,-1);
        }
    }
    //通知用户



    protected function asyn_sms_send($data) {
        $param = http_build_query($data);
        $fp = @fsockopen('ssl://' . $this->domain, 443, $errno, $errstr, 20);
        if (!$fp){
            $this->msglog($this->cmd,'error fsockopen');
        }else{
            stream_set_blocking($fp,0);
            $http = "GET /api/notifysms/goodsOrder?".$param." HTTP/1.1\r\n";
            $http .= "Host: ".$this->domain."\r\n";
            $http .= "Connection: Close\r\n\r\n";
            fwrite($fp,$http);
            usleep(1000);
            fclose($fp);
        }
    }


}