<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/8
 * Time: 11:11
 */

namespace app\api\controller;
use my\Sendsms;
use EasyWeChat\Factory;
use think\Db;
use think\Exception;

class Api extends Base
{

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
    //商品列表
    public function activityDetail() {
        $post['activity_id'] = input('post.activity_id','');
        checkPost($post);
        $whereGoods = [
            ['g.status','=',1],
            ['g.activity_id','=',$post['activity_id']],
            ['g.del','=',0]
        ];
        $whereActivity = [
            ['id','=',$post['activity_id']]
        ];
        $order = ['g.sort'=>'ASC','g.id'=>'DESC'];
        try {
            $exist = Db::table('mp_activity')->where($whereActivity)->find();
            if(!$exist) {
                return ajax('活动id不存在',-4);
            }
            $list = Db::table('mp_goods')->alias('g')
                ->join('mp_museum m','g.museum_id=m.id','left')
                ->join('mp_goods_cate c','g.cate_id=c.id','left')
                ->where($whereGoods)
                ->order($order)
                ->field('g.id,g.name,g.origin_price,g.price,g.pics,g.sales,g.desc,m.museum_name,c.cate_name')
                ->select();
        } catch(\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        foreach ($list as &$v) {
            $v['pics'] = unserialize($v['pics']);
        }
        $exist['list'] = $list;
        $exist['byyl'] = 'https://shop.bwg.art/static/byyl.jpg?123';
        $exist['qttj'] = 'https://shop.bwg.art/static/qttj.png';
        $exist['zhtz'] = 'https://shop.bwg.art/static/zhtz.png';
        $exist['comb'] = 'https://shop.bwg.art/static/comb.png';
        return ajax($exist);
    }
    //博物馆列表
    public function museumList() {
        try {
            $whereMuseum = [];
            $list = Db::table('mp_museum')->where($whereMuseum)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }
    //商品列表
    public function goodsList() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $type = input('post.type',0);
        $museum_id = input('post.museum_id',0);
        $cate_id = input('post.cate_id',0);
        $search = input('post.search','');


        $curr_page = $curr_page ? $curr_page : 1;
        $perpage = $perpage ? $perpage : 10;
        $whereGoods = [
            ['g.status','=',1],
            ['g.activity_id','=',0],
            ['g.del','=',0]
        ];
        switch ($type) {
            case 1://限时价
                $whereGoods[] = ['g.time_limit_price','=',1];
                ;break;
            case 2://尖货推荐
                $whereGoods[] = ['g.recommend','=',1];
                ;break;
            default:;
        }
        $order = ['g.id'=>'DESC'];

        if($cate_id) {
            $whereGoods[] = ['g.cate_id','=',$cate_id];
        }
        if($museum_id) {
            $whereGoods[] = ['g.museum_id','=',$museum_id];
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
                ->field('g.id,g.name,g.origin_price,g.price,g.pics,g.sales,g.desc,m.museum_name,c.cate_name')
                ->select();
        } catch(\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        foreach ($list as &$v) {
            $v['pics'] = unserialize($v['pics']);
        }
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

    public function videoList() {
        try {
            $whereVideo = [
                ['id','=',1]
            ];
            $video_exist = Db::table('mp_video')->where($whereVideo)->field('id,title,poster,video_url')->find();
        } catch(\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($video_exist);
    }

    //关于我们
    public function aboutUs() {
        try {
            $wherePlat = [
                ['id','=',1]
            ];
            $info = Db::table('mp_plat')->where($wherePlat)->find();
            if(!$info) {
                return ajax('data not exists',-1);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($info);
    }


    //收集formid
    public function collectFormid() {
        $val['formid'] = input('post.formid');
        checkPost($val);
        if($val['formid'] == 'the formId is a mock one') {
            return ajax();
        }
        $val['uid'] = $this->myinfo['id'];
        $val['create_time'] = time();
        try {
            Db::table('mp_formid')->insert($val);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($val);
    }


    public function getQrcode()
    {
        $uid = $this->myinfo['id'];
//        $uid = 1999;
        $app = Factory::miniProgram($this->mp_config);
        $response = $app->app_code->getUnlimit($uid, [
            'page' => 'pages/auth/auth',
            'width' => '300'
        ]);
        $png = $uid . '.png';
        $save_path = 'upload/appcode/';
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $filename = $response->saveAs($save_path, $png);
        } else {
            return ajax($response, -1);
        }
        return ajax($save_path . $png);
    }








}