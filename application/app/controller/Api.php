<?php
namespace app\app\controller;
use think\Db;
class Api extends Base {

    /**
     *                    _ooOoo_
     *                   o8888888o
     *                   88" . "88
     *                   (| -_- |)
     *                    O\ = /O
     *                ____/`---'\____
     *              .   ' \\| |// `.
     *               / \\||| : |||// \
     *             / _||||| -:- |||||- \
     *               | | \\\ - /// | |
     *             | \_| ''\---/'' | |
     *              \ .-\__ `-` ___/-. /
     *           ___`. .' /--.--\ `. . __
     *        ."" '< `.___\_<|>_/___.' >'"".
     *       | | : `- \`.;`\ _ /`;.`/ - ` : | |
     *         \ \ `-. \_ __\ /__ _/ .-` / /
     * ======`-.____`-.___\_____/___.-`____.-'======
     *                    `=---='
     *
     * .............................................
     *          佛祖保佑             永无BUG
     */
    public function wxPay() {
        $appid = config('appid');
        $mch_id = config('mch_id');
        $pay_order_sn = create_unique_number('');
        $total_price = 0.02;
        $arr = [
            'appid' => $appid,
            'mch_id' => $mch_id,
            'nonce_str' => randomkeys(32),
            'sign_type' => 'MD5',
            'body' => '山洞文创APP',
            'out_trade_no' => $pay_order_sn,
            'total_fee' => floatval($total_price)*100,
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'notify_url' => $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/app/api/wxPayNotify",
            'trade_type' => 'APP'
        ];

        $arr['sign'] = getSign($arr);

        /*--------------微信统一下单--------------*/
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $res = curl_post_data($url, array2xml($arr));
        $result = xml2array($res);
        try {
            if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                $prepay['appid'] = $result['appid'];
                $prepay['partnerid'] = $result['mch_id'];
                $prepay['prepayid'] = $result['prepay_id'];
                $prepay['package'] = 'Sign=WXPay';
                $prepay['noncestr'] = $result['nonce_str'];
                $prepay['timestamp'] = strval(time());
                $prepay['sign'] = getSign($prepay);
            } else {
                return ajax('微信下单失败',53);
            }
        } catch(\Exception $e) {
            return ajax($e->getMessage(),-1);
        }

        return ajax($prepay);

    }

    //获取轮播图列表
    public function slideList() {
        $where = [
            ['status', '=', 1],
            ['type', '=', 1]
        ];
        try {
            $list = Db::table('mp_slideshow')->where($where)
                ->field('id,title,url,pic')
                ->order(['sort' => 'ASC'])->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }

    //商品列表
    public function goodsList() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $cate_id = input('post.cate_id','');
        $search = input('post.search','');
        $curr_page = $curr_page ? $curr_page : 1;
        $perpage = $perpage ? $perpage : 10;
        $whereGoods = [
            ['g.status','=',1],
            ['g.del','=',0]
        ];
        $order = ['g.id'=>'DESC'];
        if($cate_id) {
            $whereGoods[] = ['g.cate_id','=',$cate_id];
        }
        if($search) {
            $whereGoods[] = ['g.name','LIKE',"%{$search}%"];
        }

        try {
            $list = Db::table('mp_goods')->alias('g')
                ->join('mp_museum m','g.museum_id=m.id','left')
                ->join('mp_goods_cate c','g.cate_id=c.id','left')
                ->where($whereGoods)
                ->order($order)
                ->limit(($curr_page-1)*$perpage,$perpage)
                ->field('g.id,g.name,g.pics,g.price,c.cate_name,m.museum_name')
                ->select();
        } catch(\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        foreach ($list as &$v) {
            $v['pic'] = unserialize($v['pics'])[0];
        }
        usleep(300000);
        return ajax($list);
    }

    //商品详情
    public function goodsDetail() {
        $val['goods_id'] = input('post.goods_id');
        checkPost($val);
        try {
            $where = [
                ['g.id','=',$val['goods_id']]
            ];
            $info = Db::table('mp_goods')->alias('g')
                ->join('mp_museum m','g.museum_id=m.id','left')
                ->join('mp_goods_cate c','g.cate_id=c.id','left')
                ->where($where)
                ->field("g.id,g.name,g.detail,g.origin_price,g.price,g.time_limit_price,g.pics,g.carriage,g.stock,g.sales,g.use_attr,g.attr,m.museum_name,c.cate_name")
                ->find();
            if(!$info) {
                return ajax($val['goods_id'],-4);
            }
            if($info['use_attr']) {
                $whereAttr = [
                    ['goods_id','=',$val['goods_id']],
                    ['del','=',0]
                ];
                $attr_list = Db::table('mp_goods_attr')->where($whereAttr)->select();
            }else {
                $attr_list = [];
            }
            $info['attr_list'] = $attr_list;
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $info['pics'] = unserialize($info['pics']);
        return ajax($info);
    }

    //商品分类列表
    public function cateList() {
        try {
            $whereCate = [
                ['del','=',0],
                ['status','=',1],
                ['pid','=',0]
            ];
            $list = Db::table('mp_goods_cate')->where($whereCate)->select();
        } catch(\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }

    //微信充值支付回调
    public function wxPayNotify() {
        //将返回的XML格式的参数转换成php数组格式
        $xml = file_get_contents('php://input');
        $data = xml2array($xml);
        if($data) {
            $this->paylog($this->cmd,var_export($data,true));
        }
        exit(array2xml(['return_code'=>'SUCCESS','return_msg'=>'OK']));
    }

    public function test() {
        halt($_SERVER);
    }



}