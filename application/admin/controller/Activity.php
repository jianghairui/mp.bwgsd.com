<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/8
 * Time: 18:21
 */
namespace app\admin\controller;
use think\Exception;
use think\Db;
class Activity extends Base {

    //轮播图列表
    public function activityList() {
        $page['query'] = http_build_query(input('param.'));
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        try {
            $where = [];
            $count = Db::table('mp_activity')->where($where)->count();

            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_activity')->where($where)
                ->order(['id'=>'DESC'])
                ->limit(($curr_page-1)*$perpage,$perpage)->select();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();

    }

    public function activityAdd() {
        return $this->fetch();
    }
    //添加轮播图POST
    public function activityAddPost() {
        $val['title'] = input('post.title');
        checkInput($val);
        try {
            Db::table('mp_activity')->insert($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //修改轮播图
    public function activityDetail() {
        $val['id'] = input('param.id');
        try {
            $exist = Db::table('mp_activity')->where('id','=',$val['id'])->find();
            if(!$exist) {
                die('DIE');
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$exist);
        return $this->fetch();
    }
    //修改轮播图POST
    public function activityMod() {
        $val['title'] = input('post.title');
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $exist = Db::table('mp_activity')->where('id',$val['id'])->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_activity')->update($val);
        }catch (Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
    //删除轮播图
    public function activityDel() {
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $whereactivity = [
                ['id','=',$val['id']]
            ];
            $exist = Db::table('mp_activity')->where($whereactivity)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_activity')->where($whereactivity)->delete();
            $whereGoods = [
                ['activity_id','=',$val['id']]
            ];
            Db::table('mp_goods')->where($whereGoods)->update(['activity_id'=>0,'status'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }


}