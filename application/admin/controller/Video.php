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

    }

    public function videoAdd() {
        if(request()->isPost()) {
            $val['title'] = input('post.title');
            checkInput($val);
            $poster_url = input('post.poster','');
            $video_url = input('post.video_url','');
            try {
                if($poster_url) {
                    $qiniu_exist = $this->qiniuFileExist($poster_url);
                    if($qiniu_exist !== true) {
                        return ajax($qiniu_exist['msg'],-1);
                    }
                    $qiniu_move = $this->moveFile($video_url,'bwgsd/poster/');
                    if($qiniu_move['code'] == 0) {
                        $val['video_url'] = $qiniu_move['path'];
                    }else {
                        return ajax($qiniu_move['msg'],-2);
                    }
                }else {
                    return ajax('请上传封面图',-1);
                }
                if($video_url) {
                    $qiniu_exist = $this->qiniuFileExist($video_url);
                    if($qiniu_exist !== true) {
                        return ajax($qiniu_exist['msg'],-1);
                    }
                    $qiniu_move = $this->moveFile($video_url,'bwgsd/video/');
                    if($qiniu_move['code'] == 0) {
                        $val['video_url'] = $qiniu_move['path'];
                    }else {
                        return ajax($qiniu_move['msg'],-2);
                    }
                }else {
                    return ajax('请上传视频',-1);
                }

            }catch (\Exception $e) {
                $this->rs_delete($val['video_url']);
                return ajax($e->getMessage(),-1);
            }
            return ajax([],1);
        }
        return $this->fetch();
    }

    public function videoMod() {

    }

    public function videoDel() {

    }

}