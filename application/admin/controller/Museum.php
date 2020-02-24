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
        try {
            $count = Db::table('mp_museum')->where($whereAttr)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_museum')->where($whereAttr)->limit(($curr_page-1)*$perpage,$perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }
    //添加博物馆
    public function museumAdd() {
        if(request()->isPost()) {
            $val['museum_name'] = input('post.museum_name');
            checkInput($val);
            try {
                if(isset($_FILES['file'])) {
                    $info = upload('file',$this->upload_base_path . 'museum/');
                    if($info['error'] === 0) {
                        $val['cover'] = $info['data'];
                    }else {
                        return ajax($info['msg'],-1);
                    }
                }else {
                    return ajax('请上传图片',-1);
                }
                Db::table('mp_museum')->insert($val);
            } catch (\Exception $e) {
                if(isset($val['cover'])) {
                    @unlink($val['cover']);
                }
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
        return $this->fetch();
    }
    //编辑博物馆
    public function museumMod() {
        if(request()->isPost()) {
            $val['museum_name'] = input('post.museum_name');
            $val['id'] = input('post.id');
            checkInput($val);
            try {
                $whereAttr = [
                    ['id','=',$val['id']]
                ];
                $museum_exist = Db::table('mp_museum')->where($whereAttr)->find();
                if(!$museum_exist) {
                    return ajax('非法参数',-1);
                }
                if(isset($_FILES['file'])) {
                    $info = upload('file',$this->upload_base_path . 'museum/');
                    if($info['error'] === 0) {
                        $val['cover'] = $info['data'];
                    }else {
                        return ajax($info['msg'],-1);
                    }
                }
                Db::table('mp_museum')->where($whereAttr)->update($val);
            } catch (\Exception $e) {
                if(isset($val['cover'])) {
                    @unlink($val['cover']);
                }
                return ajax($e->getMessage(), -1);
            }
            if(isset($val['cover'])) {
                @unlink($museum_exist['cover']);
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
                $card_exist = Db::table('mp_goods')->where($whereGoods)->find();
                if($card_exist) {
                    return ajax('此博物馆下有商品,无法删除',-1);
                }
                Db::table('mp_museum')->where($whereMuseum)->delete();
            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
    }




}