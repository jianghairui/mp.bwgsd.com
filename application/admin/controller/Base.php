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

require_once ROOT_PATH . '/extend/qiniu/autoload.php';
use Qiniu\Config;
use Qiniu\Storage\BucketManager;
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





    //七牛云判断文件是否存在
    public function qiniuFileExist($key) {
        $auth = new \Qiniu\Auth(config('qiniu_ak'), config('qiniu_sk'));
        $config = new Config();
        $bucketManager = new BucketManager($auth, $config);
        list($fileInfo, $err) = $bucketManager->stat(config('qiniu_bucket'), $key);
        if ($err) {
            return [
                'code' => 1,
                'msg' => 'qiniu_code:' . $err->code() .' , '. $err->message()
            ];
        }
        return true;
    }

    //七牛云移动文件
    protected function moveFile($srcKey,$destpath='upload/public/') {
        $auth = new \Qiniu\Auth(config('qiniu_ak'), config('qiniu_sk'));
        $config = new Config();
        $bucketManager = new BucketManager($auth, $config);

        $srcBucket = config('qiniu_bucket');
        $destBucket = config('qiniu_bucket');
        $arr = explode('/',$srcKey);
        $destKey = $destpath . end($arr);
        //如果一样不需要挪动
        if($srcKey == $destKey) {
            return [
                'code' => 0,
                'path' => $destKey
            ];
        }
        $err = $bucketManager->move($srcBucket, $srcKey, $destBucket, $destKey, true);
        if($err) {
            return [
                'code' => 1,
                'msg' => 'qiniu_code:' . $err->code() .' , '. $err->message()
            ];
        }else {
            return [
                'code' => 0,
                'path' => $destKey
            ];
        }

    }

    //七牛云删除文件
    protected function rs_delete($key) {
        $auth = new \Qiniu\Auth(config('qiniu_ak'), config('qiniu_sk'));
        $config = new Config();
        $bucketManager = new BucketManager($auth, $config);
        $bucketManager->delete(config('qiniu_bucket'), $key);
    }

    public function qiniuLog($cmd,$str) {
        $file= ROOT_PATH . '/log/qiniu_error.log';
        $text='[Time ' . date('Y-m-d H:i:s') ."]\ncmd:" .$cmd. "\n" .$str. "\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }





}