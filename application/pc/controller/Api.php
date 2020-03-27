<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/8
 * Time: 11:11
 */

namespace app\pc\controller;
use think\Controller;
use think\Db;
use think\Exception;

class Api extends Controller
{

    //获取轮播图列表
    public function slideList() {
        $where = [
            ['status', '=', 1],
            ['type', '=', 2]
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
    public function goodsList() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $cate_id = input('post.cate_id','');
        $search = input('post.search','');
        $type = input('post.type',0);


        $curr_page = $curr_page ? $curr_page : 1;
        $perpage = $perpage ? $perpage : 10;
        $whereGoods = [
            ['g.status','=',1]
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
            $whereGoods = [
                ['use_video','=',1]
            ];
            $list = Db::table('mp_goods')->where($whereGoods)->field('id,name,price,video_url')->select();
        } catch(\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
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





}