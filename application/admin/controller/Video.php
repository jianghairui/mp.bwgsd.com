<?php
namespace app\admin\controller;

use think\Db;
class Video extends Base {

    public function videoList() {
        $page['query'] = http_build_query(input('param.'));
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $where = [];
        try {
            $count = Db::table('mp_video')->where($where)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_video')
                ->where($where)
                ->limit(($curr_page - 1)*$perpage,$perpage)
                ->order(['id'=>'DESC'])
                ->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }

        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }

    public function videoDetail() {
        $param['id'] = input('param.id','');
        try {
            $whereVideo = [
                ['id','=',$param['id']]
            ];
            $video_exist = Db::table('mp_video')->where($whereVideo)->find();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('info',$video_exist);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }

    public function videoAdd() {
        if(request()->isPost()) {
            $post['title'] = input('post.title');
            checkInput($post);
            $poster_url = input('post.poster','');
            $video_url = input('post.video_url','');

            if(!$poster_url) {return ajax('请上传封面图',-1);}
            if(!$video_url) {return ajax('请上传视频',-1);}
            try {
                $insert_data['title'] = $post['title'];

                $qiniu_exist = $this->qiniuFileExist($poster_url);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }
                $qiniu_move = $this->moveFile($poster_url,'bwgsd/poster/');
                if($qiniu_move['code'] == 0) {
                    $insert_data['poster'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-2);
                }

                $qiniu_exist = $this->qiniuFileExist($video_url);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }
                $qiniu_move = $this->moveFile($video_url,'bwgsd/video/');
                if($qiniu_move['code'] == 0) {
                    $insert_data['video_url'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-2);
                }
                Db::table('mp_video')->insert($insert_data);
            }catch (\Exception $e) {
                $this->rs_delete($insert_data['poster']);
                $this->rs_delete($insert_data['video_url']);
                return ajax($e->getMessage(),-1);
            }
            return ajax([],1);
        }
        return $this->fetch();
    }

    public function videoMod() {
        if(request()->isPost()) {
            $post['title'] = input('post.title');
            $post['id'] = input('post.id');
            checkInput($post);
            $poster_url = input('post.poster','');
            $video_url = input('post.video_url','');

            if(!$poster_url) {return ajax('请上传封面图',-1);}
            if(!$video_url) {return ajax('请上传视频',-1);}
            try {
                $whereVideo = [
                    ['id','=',$post['id']]
                ];
                $video_exist = Db::table('mp_video')->where($whereVideo)->find();
                if(!$video_exist) {
                    return ajax('非法操作',-1);
                }
                $update_data['title'] = $post['title'];
                $qiniu_exist = $this->qiniuFileExist($poster_url);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }
                $qiniu_move = $this->moveFile($poster_url,'bwgsd/poster/');
                if($qiniu_move['code'] == 0) {
                    $update_data['poster'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-2);
                }

                $qiniu_exist = $this->qiniuFileExist($video_url);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }
                $qiniu_move = $this->moveFile($video_url,'bwgsd/video/');
                if($qiniu_move['code'] == 0) {
                    $update_data['video_url'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-2);
                }
                Db::table('mp_video')->where($whereVideo)->update($update_data);
            }catch (\Exception $e) {
                if($poster_url != $video_exist['poster']) {
                    $this->rs_delete($poster_url);
                }
                if($video_url != $video_exist['video_url']) {
                    $this->rs_delete($video_url);
                }
                return ajax($e->getMessage(),-1);
            }
            if($poster_url != $video_exist['poster']) {
                $this->rs_delete($video_exist['poster']);
            }
            if($video_url != $video_exist['video_url']) {
                $this->rs_delete($video_exist['video_url']);
            }
            return ajax([],1);
        }
    }

    public function videoDel() {
        if(request()->isPost()) {
            $post['id'] = input('post.id');
            checkInput($post);
            try {
                $whereVideo = [
                    ['id','=',$post['id']]
                ];
                $video_exist = Db::table('mp_video')->where($whereVideo)->find();
                if(!$video_exist) {
                    return ajax('非法操作',-1);
                }
                Db::table('mp_video')->where($whereVideo)->delete();
            }catch (\Exception $e) {
                return ajax($e->getMessage(),-1);
            }
            $this->rs_delete($video_exist['poster']);
            $this->rs_delete($video_exist['video_url']);
            return ajax([],1);
        }
    }

}