<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2019/12/10
 * Time: 13:45
 */
namespace app\admin\controller;

use think\Db;
class Museum extends Base {

    //博物馆列表
    public function museumList() {
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $page['query'] = http_build_query(input('param.'));
        $whereAttr = [];
        $order = ['id'=>'DESC'];
        try {
            $count = Db::table('mp_museum')->where($whereAttr)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_museum')->where($whereAttr)->limit(($curr_page-1)*$perpage,$perpage)->order($order)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }
    //添加博物馆
    public function museumAdd() {
        if(request()->isPost()) {
            $val['museum_name'] = input('post.museum_name');
            $val['cover'] = input('post.cover');
            checkInput($val);
            try {
                $whereMuseum = [
                    ['museum_name','=',$val['museum_name']]
                ];
                $museum_exist = Db::table('mp_museum')->where($whereMuseum)->find();
                if($museum_exist) {
                    return ajax('博物馆已存在',-1);
                }
                $qiniu_exist = $this->qiniuFileExist($val['cover']);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }
                $qiniu_move = $this->moveFile($val['cover'],'upload/museum/');
                if($qiniu_move['code'] == 0) {
                    $val['cover'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-1);
                }
                Db::table('mp_museum')->insert($val);
            } catch (\Exception $e) {
                $this->rs_delete($val['cover']);
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
        return $this->fetch();
    }
    //博物馆详情
    public function museumDetail() {
        $val['id'] = input('param.id');
        checkInput($val);
        try {
            $whereAttr = [
                ['id','=',$val['id']]
            ];
            $museum_exist = Db::table('mp_museum')->where($whereAttr)->find();
            if(!$museum_exist) {
                die('非法参数');
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('info',$museum_exist);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }
    //编辑博物馆
    public function museumMod() {
        if(request()->isPost()) {
            $val['museum_name'] = input('post.museum_name');
            $val['id'] = input('post.id');
            $val['cover'] = input('post.cover');
            checkInput($val);
            try {
                $whereAttr = [
                    ['id','=',$val['id']]
                ];
                $museum_exist = Db::table('mp_museum')->where($whereAttr)->find();
                if(!$museum_exist) {
                    return ajax('非法参数',-1);
                }
                $whereMuseum = [
                    ['museum_name','=',$val['museum_name']],
                    ['id','<>',$val['id']]
                ];
                $name_exist = Db::table('mp_museum')->where($whereMuseum)->find();
                if($name_exist) {
                    return ajax('博物馆已存在',-1);
                }
                $qiniu_exist = $this->qiniuFileExist($val['cover']);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }
                $qiniu_move = $this->moveFile($val['cover'],'upload/museum/');
                if($qiniu_move['code'] == 0) {
                    $val['cover'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-1);
                }
                Db::table('mp_museum')->where($whereAttr)->update($val);
            } catch (\Exception $e) {
                if($val['cover'] !== $museum_exist['cover']) {
                    $this->rs_delete($val['cover']);
                }
                return ajax($e->getMessage(), -1);
            }
            if($val['cover'] !== $museum_exist['cover']) {
                $this->rs_delete($museum_exist['cover']);
            }
            return ajax();
        }
    }
    //删除博物馆
    public function museumDel() {
        if(request()->isPost()) {
            $val['id'] = input('post.id');
            checkInput($val);
            try {
                $whereMuseum = [
                    ['id','=',$val['id']]
                ];
                $museum_exist = Db::table('mp_museum')->where($whereMuseum)->find();
                if(!$museum_exist) {
                    return ajax('非法参数',-1);
                }
                $whereGoods = [
                    ['museum_id','=',$val['id']]
                ];
                Db::table('mp_goods')->where($whereGoods)->update(['del'=>1,'museum_id'=>0]);
//                $card_exist = Db::table('mp_goods')->where($whereGoods)->find();
//                if($card_exist) {
//                    return ajax('此博物馆下有商品,无法删除',-1);
//                }
                Db::table('mp_museum')->where($whereMuseum)->delete();
            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            $this->rs_delete($museum_exist['cover']);
            return ajax();
        }
    }




}