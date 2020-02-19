<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/4/18
 * Time: 18:17
 */
namespace app\admin\controller;
use think\Controller;
use my\Auth;
use think\exception\HttpResponseException;
class Base extends Controller {

    protected $cmd;
    protected $controller;
    protected $upload_base_path;
    protected $rename_base_path;
    protected $config;
    protected $mp_config;

    public function initialize() {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->cmd = request()->controller() . '/' . request()->action();
        $this->controller = request()->controller();
        $this->upload_base_path = 'upload/admin/';
        $this->rename_base_path = 'upload/api/';
        /*-------山洞公众号------*/
        $this->mp_config = [
            'appid' => config('appid'),
            'app_secret' => config('app_secret'),
            'mch_id' => config('mch_id')
        ];

        if(!$this->needSession()) {
            if(request()->isPost()) {
                throw new HttpResponseException(ajax([],-5));
            }else {
                $this->error('请登录后操作',url('Login/index'));
            }
        }

    }

    private function needSession() {
        $noNeedSession = [
            'Login/index',
            'Login/vcode',
            'Login/login',
            'Api',
            'Test',
        ];
        if (in_array($this->cmd, $noNeedSession) || in_array($this->controller, $noNeedSession)) {
            return true;
        }else {
            if(session('username') && session('loginstatus') && session('loginstatus') == md5(session('username') . config('login_key'))) {
                if(session('username') !== config('superman')) {
                    $auth = new Auth();
                    $bool = $auth->check($this->cmd,session('admin_id'));
                    if(!$bool) {
                        if(request()->isPost()) {
                            throw new HttpResponseException(ajax('没有权限',-1));
                        }else {
                            exit($this->fetch('public/noAuth'));
                        }
                    }
                }
                return true;
            }else {
                return false;
            }
        }
    }

    //公众号开发检验签名
    protected function checkSignature($param)
    {
        $token = $this->token;
        $tmpArr = array($token,$param["timestamp"], $param["nonce"]);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode('',$tmpArr);
        $tmpStr = sha1( $tmpStr );
        if($tmpStr === $param["signature"]){
            return true;
        }else{
            return false;
        }
    }

    //微信日志
    protected function weixinlog($cmd = '',$msg = '') {
        $file= LOG_PATH . '/wechat.log';
        create_dir($file);
        $text='[Time ' . date('Y-m-d H:i:s') ."]\ncmd:".$cmd."\n".$msg."\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }

    //xml数据日志
    protected function xmllog($cmd = '',$msg = '') {
        $file= LOG_PATH . '/xml.log';
        create_dir($file);
        $text='[Time ' . date('Y-m-d H:i:s') ."]\ncmd:".$cmd."\n".$msg."\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }

    //Exception日志
    protected function excep($cmd,$str) {
        $file= LOG_PATH . '/exception.log';
        create_dir($file);
        $text='[Time ' . date('Y-m-d H:i:s') ."]\ncmd:" .$cmd. "\n" .$str. "\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }





}