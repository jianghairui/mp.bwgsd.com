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

class Test extends Controller
{

    //商品列表
    private function goodsList() {
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
                ->field('g.id,g.name,g.pics,g.price,c.cate_name')
                ->select();
        } catch(\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        foreach ($list as &$v) {
            $v['pic'] = unserialize($v['pics'])[0];
        }
        return ajax($list);
    }


}