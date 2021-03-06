<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/3/19
 * Time: 13:45
 */
namespace app\admin\controller;
use think\Db;
use my\Kuaidiniao;

class Shop extends Base {
//商品列表
    public function goodsList() {
        $param['search'] = input('param.search');
        $param['cate_id'] = input('param.cate_id');
        $param['museum_id'] = input('param.museum_id','');
        $param['activity_id'] = input('param.activity_id','');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $where = [
            ['g.del','=',0]
        ];

        if($param['cate_id']) {
            $where[] = ['g.cate_id','=',$param['cate_id']];
        }
        if($param['museum_id']) {
            $where[] = ['g.museum_id','=',$param['museum_id']];
        }
        if($param['activity_id'] !== '') {
            $where[] = ['g.activity_id','=',$param['activity_id']];
        }
        if($param['search']) {
            $where[] = ['g.name','like',"%{$param['search']}%"];
        }

        try {
            $count = Db::table('mp_goods')->alias('g')->where($where)->count();

            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_goods')->alias('g')
                ->join('mp_goods_cate c','g.cate_id=c.id','left')
                ->where($where)
                ->limit(($curr_page - 1)*$perpage,$perpage)
                ->order(['g.id'=>'DESC'])
                ->field('g.*,c.cate_name')
                ->select();
            $museum_list = Db::table('mp_museum')->order(['id'=>'DESC'])->select();
            $activity_list = Db::table('mp_activity')->order(['id'=>'DESC'])->select();
            $cate_list = Db::table('mp_goods_cate')->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('cate_list',$cate_list);
        $this->assign('museum_list',$museum_list);
        $this->assign('activity_list',$activity_list);
        $this->assign('param',$param);
        $this->assign('page',$page);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }
//添加商品
    public function goodsAdd() {
        try {
            $where = [
                ['pid','=',0],
                ['del','=',0]
            ];
            $cate_list = Db::table('mp_goods_cate')->where($where)->select();
            $museum_list = Db::table('mp_museum')->order(['id'=>'DESC'])->select();
            $activity_list = Db::table('mp_activity')->order(['id'=>'DESC'])->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('cate_list',$cate_list);
        $this->assign('museum_list',$museum_list);
        $this->assign('activity_list',$activity_list);
        return $this->fetch();
    }
//商品详情
    public function goodsDetail() {
        $id = input('param.id');
        try {
            $info = Db::table('mp_goods')->where('id','=',$id)->find();
            if(!$info) {
                die('非法参数');
            }
            $where_attr = [
                ['goods_id','=',$id],
                ['del','=',0]
            ];
            $attr_list = Db::table('mp_goods_attr')->where($where_attr)->select();
            $where = [
                ['pid','=',0],
                ['del','=',0]
            ];
            $cate_list = Db::table('mp_goods_cate')->where($where)->select();
            $museum_list = Db::table('mp_museum')->order(['id'=>'DESC'])->select();
            $activity_list = Db::table('mp_activity')->order(['id'=>'DESC'])->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('cate_list',$cate_list);
        $this->assign('museum_list',$museum_list);
        $this->assign('activity_list',$activity_list);
        $this->assign('info',$info);
        $this->assign('attr_list',$attr_list);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }
//添加商品POST
    public function goodsAddPost() {
        $val['cate_id'] = input('post.cate_id');
        $val['museum_id'] = input('post.museum_id');
        $val['activity_id'] = input('post.activity_id');
        $val['name'] = input('post.name');
        $val['origin_price'] = input('post.origin_price');
        $val['price'] = input('post.price');
        $val['stock'] = input('post.stock');
        $val['sort'] = input('post.sort');
        $val['hot'] = input('post.hot');
        $val['sales'] = input('post.sales');
        $val['status'] = input('post.status');
        $val['unit'] = input('post.unit');
        $val['carriage'] = input('post.carriage');
        $val['service'] = input('post.service');
        $val['status'] = input('post.status');
        checkInput($val);
        $val['create_time'] = time();
        $val['desc'] = input('post.desc');
        $val['detail'] = input('post.detail');
        $val['use_attr'] = input('post.use_attr',0);
        $image = input('post.pic_url',[]);

        $use_video = input('post.use_video',0);
        $video_url = input('post.video_url');

        try {
            if($val['use_attr']) {
                $val['stock'] = 0;
                $attr1 = input('post.attr1',[]);
                $attr2 = input('post.attr2',[]);
                $attr3 = input('post.attr3',[]);

                $val['attr'] = input('post.attr','');
                if(!$val['attr'] || empty($attr1)) {
                    return ajax('至少添加一个规格',-1);
                }
                if(count($attr1) !== count($attr2) || count($attr1) !== count($attr3)) {
                    return ajax('属性规格异常',-1);
                }
                foreach ($attr1 as $v) {
                    if(!$v) {
                        return ajax('属性规格值不能为空',-1);
                    }
                }
                foreach ($attr2 as $v) {
                    if(!is_currency($v)) {
                        return ajax('属性金额格式不合法',-1);
                    }
                }
                foreach ($attr3 as $v) {
                    if(!if_int($v)) {
                        return ajax('规格库存必须为数字',-1);
                    }
                    $val['stock'] += $v;
                }
            }

            if($use_video) {
                $val['use_video'] = 1;
                $qiniu_exist = $this->qiniuFileExist($video_url);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }
                $qiniu_move = $this->moveFile($video_url,'bwgsd/goodsvideo/');
                if($qiniu_move['code'] == 0) {
                    $val['video_url'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-2);
                }
            }

            $limit = 9;
            $image_array = [];
            if(is_array($image) && !empty($image)) {
                if(count($image) > $limit) {
                    return ajax('最多上传'.$limit.'张图片',-1);
                }
                foreach ($image as $v) {
                    $qiniu_exist = $this->qiniuFileExist($v);
                    if($qiniu_exist !== true) {
                        return ajax('图片已失效请重新上传',-1);
                    }
                }
            }else {
                return ajax('请上传商品图片',-1);
            }
            foreach ($image as $v) {
                $qiniu_move = $this->moveFile($v,'upload/goods/');
                if($qiniu_move['code'] == 0) {
                    $image_array[] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-3);
                }
            }
            $val['pics'] = serialize($image_array);
            $goods_id = Db::table('mp_goods')->insertGetId($val);
            if($val['use_attr']) {
                $attr_insert = [];
                foreach ($attr1 as $k=>$v) {
                    $data['goods_id'] = $goods_id;
                    $data['value'] = $attr1[$k];
                    $data['price'] = $attr2[$k];
                    $data['stock'] = $attr3[$k];
                    $data['create_time'] = time();
                    $attr_insert[] = $data;
                }
                Db::table('mp_goods_attr')->insertAll($attr_insert);
            }
        }catch (\Exception $e) {
            $this->rs_delete($val['video_url']);
            foreach ($image_array as $v) {
                $this->rs_delete($v);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
//修改商品POST
    public function goodsMod() {
        $val['cate_id'] = input('post.cate_id');
        $val['museum_id'] = input('post.museum_id');
        $val['activity_id'] = input('post.activity_id');
        $val['name'] = input('post.name');
        $val['origin_price'] = input('post.origin_price');
        $val['price'] = input('post.price');
        $val['stock'] = input('post.stock');
        $val['sort'] = input('post.sort');
        $val['hot'] = input('post.hot');
        $val['sales'] = input('post.sales');
        $val['status'] = input('post.status');
        $val['unit'] = input('post.unit');
        $val['carriage'] = input('post.carriage');
        $val['service'] = input('post.service');
        $val['status'] = input('post.status');
        $val['id'] = input('post.id');
        checkInput($val);
        $val['desc'] = input('post.desc');
        $val['detail'] = input('post.detail');
        $val['create_time'] = time();
        $val['use_attr'] = input('post.use_attr',0);
        $image = input('post.pic_url',[]);
        $use_video = input('post.use_video',0);
        $video_url = input('post.video_url');
        try {
            if($val['use_attr']) {
                $val['stock'] = 0;
                $attr0 = input('post.attr0',[]);//attr_ids
                $attr1 = input('post.attr1',[]);//属性值
                $attr2 = input('post.attr2',[]);//金额
                $attr3 = input('post.attr3',[]);//库存

                $val['attr'] = input('post.attr','');//属性名
                if(!$val['attr'] || empty($attr1)) {
                    return ajax('至少添加一个规格',-1);
                }
                if(count($attr1) !== count($attr2) || count($attr1) !== count($attr3)) {
                    return ajax('属性规格异常',-1);
                }
                foreach ($attr1 as $v) {
                    if(!$v) {return ajax('属性规格值不能为空',-1);}
                }
                foreach ($attr2 as $v) {
                    if(!is_currency($v)) {return ajax('属性金额格式不合法',-1);}
                }
                foreach ($attr3 as $v) {
                    if(!if_int($v)) {return ajax('规格库存必须为数字'.$v,-1);}
                    $val['stock'] += $v;
                }

            }

            $map = [
                ['id','=',$val['id']],
                ['del','=',0]
            ];
            $goods_exist = Db::table('mp_goods')->where($map)->find();
            if(!$goods_exist) {
                return ajax('非法参数',-1);
            }
            $old_pics = unserialize($goods_exist['pics']);


            if($use_video) {
                $val['use_video'] = 1;
                $qiniu_exist = $this->qiniuFileExist($video_url);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }
                $qiniu_move = $this->moveFile($video_url,'bwgsd/goodsvideo/');
                if($qiniu_move['code'] == 0) {
                    $val['video_url'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-2);
                }
            }else {
                $val['use_video'] = 0;
            }

            $limit = 6;
            $image_array = [];
            if(is_array($image) && !empty($image)) {
                if(count($image) > $limit) {
                    return ajax('最多上传'.$limit.'张图片',-1);
                }
                foreach ($image as $v) {
                    $qiniu_exist = $this->qiniuFileExist($v);
                    if($qiniu_exist !== true) {
                        return ajax('图片已失效请重新上传',-1);
                    }
                }
            }else {
                return ajax('请上传商品图片',-1);
            }
            foreach ($image as $v) {
                $image_move = $this->moveFile($v,'upload/goods/');
                if($image_move['code'] == 0) {
                    $image_array[] = $image_move['path'];
                }else {
                    return ajax($image_move['msg'],-2);
                }
            }
            $val['pics'] = serialize($image_array);
            Db::table('mp_goods')->where($map)->update($val);
            //如果使用了规格
            if($val['use_attr']) {
                $whereAttr = [
                    ['goods_id','=',$val['id']],
                    ['del','=',0]
                ];
                $attr_ids = Db::table('mp_goods_attr')->where($whereAttr)->column('id');//原有规格
                $attr_insert = [];
                foreach ($attr1 as $k=>$v) {
                    $data['goods_id'] = $val['id'];
                    $data['value'] = $attr1[$k];
                    $data['price'] = $attr2[$k];
                    $data['stock'] = $attr3[$k];
                    if($attr0[$k] == '') {
                        $data['create_time'] = time();
                        $attr_insert[] = $data;
                    }else {
                        Db::table('mp_goods_attr')->where('id','=',$attr0[$k])->update($data);
                    }
                }
                Db::table('mp_goods_attr')->insertAll($attr_insert);
                $whereDelete = [];
                foreach ($attr_ids as $v) {
                    if(!in_array($v,$attr0)) {$whereDelete[] = $v;}
                }
                if(!empty($whereDelete)) {
                    Db::table('mp_goods_attr')->where('id','in',$whereDelete)->update(['del'=>1]);
                }
            }
        }catch (\Exception $e) {
            if($use_video) {
                if($val['video_url'] != $goods_exist['video_url']) {
                    $this->rs_delete($val['video_url']);
                }
            }
            foreach ($image_array as $v) {
                if(!in_array($v,$old_pics)) {
                    $this->rs_delete($v);
                }
            }
            return ajax($e->getMessage(),-1);
        }
        if($use_video) {
            if($val['video_url'] != $goods_exist['video_url']) {
                $this->rs_delete($goods_exist['video_url']);
            }
        }
        foreach ($old_pics as $v) {
            if(!in_array($v,$image_array)) {
                $this->rs_delete($v);
            }
        }
        return ajax([],1);
    }
//下架
    public function goodsHide() {
        $id = input('post.id','0');
        $map = [
            ['id','=',$id],
            ['status','=',1]
        ];
        try {
            $res = Db::table('mp_goods')->where($map)->update(['status'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($res) {
            return ajax();
        }else {
            return ajax('共修改0条记录',-1);
        }
    }
//上架
    public function goodsShow() {
        $id = input('post.id','0');
        $map = [
            ['id','=',$id],
            ['status','=',0]
        ];
        try {
            $res = Db::table('mp_goods')->where($map)->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($res) {
            return ajax();
        }else {
            return ajax('共修改0条记录',-1);
        }
    }

    //尖货推荐
    public function goodsRecommend() {
        $id = input('post.id','0');
        $map = [
            ['id','=',$id]
        ];
        try {
            $goods_exist = Db::table('mp_goods')->where($map)->find();
            if($goods_exist['recommend'] == 1) {
                Db::table('mp_goods')->where($map)->update(['recommend'=>0]);
                return ajax(0);
            }else {
                Db::table('mp_goods')->where($map)->update(['recommend'=>1]);
                return ajax(1);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }

    }

    //设为限时商品
    public function goodsTimeLimit() {
        $id = input('post.id','0');
        $map = [
            ['id','=',$id]
        ];
        try {
            $goods_exist = Db::table('mp_goods')->where($map)->find();
            if($goods_exist['time_limit_price'] == 1) {
                Db::table('mp_goods')->where($map)->update(['time_limit_price'=>0]);
                return ajax(0);
            }else {
                Db::table('mp_goods')->where($map)->update(['time_limit_price'=>1]);
                return ajax(1);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
    }


//删除商品
    public function goodsDel() {
        $id = input('post.id','0');
        $map = [
            ['id','=',$id]
        ];
        try {
            $res = Db::table('mp_goods')->where($map)->update(['del'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($res) {
            return ajax();
        }else {
            return ajax('共修改0条记录',-1);
        }
    }

    //商品排序
    public function sortGoods() {
        $val['id'] = input('post.id');
        $val['sort'] = input('post.sort');
        checkInput($val);
        try {
            Db::table('mp_goods')->update($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($val);
    }


//分类列表
    public function cateList() {
        $where = [
            ['del','=',0]
        ];
        try {
            $list = Db::table('mp_goods_cate')->where($where)->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }
//添加分类
    public function cateAdd() {
        return $this->fetch();
    }
//添加分类POST
    public function cateAddPost() {
        $val['cate_name'] = input('post.cate_name');
        checkInput($val);
        $icon = input('post.icon');
        try {
            if($icon) {
                $qiniu_exist = $this->qiniuFileExist($icon);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }
                $qiniu_move = $this->moveFile($icon,'upload/goodscate/');
                if($qiniu_move['code'] == 0) {
                    $val['icon'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-2);
                }
            }
            Db::table('mp_goods_cate')->insert($val);
        }catch (\Exception $e) {
            if(isset($val['icon'])) {
                $this->rs_delete($val['icon']);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax([]);
    }
//分类详情
    public function cateDetail() {
        $id = input('param.id');
        try {
            $whereCate = [
                ['id','=',$id]
            ];
            $info = Db::table('mp_goods_cate')->where($whereCate)->find();
            if(!$info) {die('非法参数');}
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$info);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));

        return $this->fetch();
    }
//修改分类POST
    public function cateMod() {
        $val['cate_name'] = input('post.cate_name');
        $val['id'] = input('post.id');
        checkInput($val);
        $icon = input('post.icon');
        try {
            $whereCate = [
                ['id','=',$val['id']]
            ];
            $cate_exist = Db::table('mp_goods_cate')->where($whereCate)->find();
            if(!$cate_exist) {
                return ajax('非法参数',-1);
            }
            if($icon) {
                $qiniu_exist = $this->qiniuFileExist($icon);
                if($qiniu_exist !== true) {
                    return ajax($qiniu_exist['msg'],-1);
                }
                $qiniu_move = $this->moveFile($icon,'upload/goodscate/');
                if($qiniu_move['code'] == 0) {
                    $val['icon'] = $qiniu_move['path'];
                }else {
                    return ajax($qiniu_move['msg'],-2);
                }
            }
            Db::table('mp_goods_cate')->where($whereCate)->update($val);
        }catch (\Exception $e) {
            if(isset($val['icon']) && $val['icon'] != $cate_exist['icon']) {
                $this->rs_delete($val['icon']);
            }
            return ajax($e->getMessage(),-1);
        }
        if(isset($val['icon']) && $val['icon'] != $cate_exist['icon']) {
            $this->rs_delete($cate_exist['icon']);
        }
        return ajax([]);
    }
//隐藏分类
    public function cateHide() {
        $id = input('post.id');
        try {
            $exist = Db::table('mp_goods_cate')->where('id',$id)->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
            Db::table('mp_goods_cate')->where('id',$id)->update(['status'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
//显示分类
    public function cateShow() {
        $id = input('post.id');
        try {
            $exist = Db::table('mp_goods_cate')->where('id',$id)->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
            Db::table('mp_goods_cate')->where('id',$id)->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
//删除分类
    public function cateDel() {
        $id = input('post.id');
        try {
            $whereCate = [
                ['id','=',$id]
            ];
            $exist = Db::table('mp_goods_cate')->where($whereCate)->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
            Db::table('mp_goods_cate')->where($whereCate)->update(['del'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
    //订单列表
    public function orderList() {
        $param['search'] = input('param.search','');
        $param['status'] = input('param.status','');
        $param['datemin'] = input('param.datemin');
        $param['datemax'] = input('param.datemax');
        $param['refund_apply'] = input('param.refund_apply','');
        $page['query'] = http_build_query(input('param.'));
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = " `del`=0";
        $order = " ORDER BY `id` DESC";
        $orderby = " ORDER BY `d`.`id` DESC";
        if($param['status'] !== '') {
            $where .= " AND status=" . $param['status'];
        }
        if($param['refund_apply']) {
            $where .= " AND refund_apply=" . $param['refund_apply'];
        }
        if($param['datemin']) {
            $where .= " AND create_time>=" . strtotime(date('Y-m-d 00:00:00',strtotime($param['datemin'])));
        }
        if($param['datemax']) {
            $where .= " AND create_time<=" . strtotime(date('Y-m-d 23:59:59',strtotime($param['datemax'])));
        }
        if($param['search']) {
            $where .= " AND (pay_order_sn LIKE '%".$param['search']."%' OR tel LIKE '%".$param['search']."%')";
        }
        try {
            $count = Db::query("SELECT count(id) AS total_count FROM mp_order o WHERE " . $where);
            $sql = "SELECT 
`o`.`id`,`o`.`pay_order_sn`,`o`.`trans_id`,`o`.`receiver`,`o`.`tel`,`o`.`address`,`o`.`pay_price`,`o`.`total_price`,`o`.`carriage`,`o`.`create_time`,`o`.`refund_apply`,`o`.`status`,`o`.`refund_apply`,`d`.`order_id`,`d`.`goods_id`,`d`.`num`,`d`.`unit_price`,`d`.`goods_name`,`d`.`attr`,`g`.`pics` 
FROM (SELECT * FROM mp_order WHERE " . $where . $order . " LIMIT ".($curr_page-1)*$perpage.",".$perpage.") `o` 
LEFT JOIN `mp_order_detail` `d` ON `o`.`id`=`d`.`order_id`
LEFT JOIN `mp_goods` `g` ON `d`.`goods_id`=`g`.`id`
" . $orderby;
            $list = Db::query($sql);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $count = $count[0]['total_count'];
        $order_id = [];
        $newlist = [];
        foreach ($list as $v) {
            $order_id[] = $v['id'];
        }
        $uniq_order_id = array_unique($order_id);
        foreach ($uniq_order_id as $v) {
            $child = [];
            foreach ($list as $li) {
                if($li['order_id'] == $v) {
                    $data['id'] = $li['id'];
                    $data['pay_order_sn'] = $li['pay_order_sn'];
                    $data['pay_price'] = $li['pay_price'];
                    $data['trans_id'] = $li['trans_id'];
                    $data['receiver'] = $li['receiver'];
                    $data['tel'] = $li['tel'];
                    $data['address'] = $li['address'];
                    $data['total_price'] = $li['total_price'];
                    $data['carriage'] = $li['carriage'];
                    $data['status'] = $li['status'];
                    $data['refund_apply'] = $li['refund_apply'];
                    $data['create_time'] = date('Y-m-d H:i',$li['create_time']);
                    $data_child['goods_id'] = $li['goods_id'];
                    $data_child['cover'] = unserialize($li['pics'])[0];
                    $data_child['goods_name'] = $li['goods_name'];
                    $data_child['num'] = $li['num'];
                    $data_child['unit_price'] = $li['unit_price'];
                    $data_child['total_price'] = sprintf ( "%1\$.2f",($li['unit_price'] * $li['num']));
                    $data_child['attr'] = $li['attr'];
                    $child[] = $data_child;
                }
            }
            $data['child'] = $child;
            $newlist[] = $data;
        }
        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        $this->assign('param',$param);
        $this->assign('list',$newlist);
        $this->assign('page',$page);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));
        return $this->fetch();
    }
    //订单发货
    public function orderSend() {
        $id = input('param.id');
        try {
            $where = [
                ['del','=',0]
            ];
            $list = Db::table('mp_tracking')->where($where)->select();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('id',$id);
        return $this->fetch();
    }
    //快递信息
    public function traceInfo() {
        $id = input('param.id');
        try {
            $whereOrder = [
                    ['status','IN',[2,3]],
                ['id','=',$id]
            ];
            $order_exist = Db::table('mp_order')->where($whereOrder)->find();
            if(!$order_exist) {
                return ajax('订单不存在或状态已改变',4);
            }
            $whereTracking = [
                ['name','=',$order_exist['tracking_name']]
            ];
            $tracking_exist = Db::table('mp_tracking')->where($whereTracking)->find();
            if(!$tracking_exist) {
                return ajax('物流不存在',-4);
            }
            $tracking_code = $tracking_exist['code'];
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $kuaidi = new Kuaidiniao();
        $result = $kuaidi->getOrderTracesByJson($tracking_code,$order_exist['tracking_num']);
        if($result['State'] == 3) {
            $traces = $result['Traces'];
        }else {
            $traces = [];
        }
//        halt($result);
        $this->assign('list',$traces);
        $this->assign('tracking_name',$order_exist['tracking_name']);
        return $this->fetch();
    }

    //确认发货
    public function deliver() {
        $val['tracking_name'] = input('post.tracking_name');
        $val['tracking_num'] = input('post.tracking_num');
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $where = [
                ['id','=',$val['id']],
                ['status','=',1]
            ];
            $exist = Db::table('mp_order')->where($where)->find();
            if(!$exist) {
                return ajax('订单不存在或状态已改变',-1);
            }
            $update_data = [
                'status' => 2,
                'send_time' => time(),
                'tracking_name' => $val['tracking_name'],
                'tracking_num' => $val['tracking_num']
            ];
            Db::table('mp_order')->where($where)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }

    //订单详情
    public function orderDetail() {
        $val['id'] = input('param.id');
        try {
            $whereOrder = [
                ['id','=',$val['id']]
            ];
            $info = Db::table('mp_order')->where($whereOrder)->find();
            if(!$info) {die('非法参数');}
            $whereDetail = [
                ['d.order_id','=',$val['id']]
            ];
            $order_detail = Db::table('mp_order_detail')->alias('d')
                ->join('mp_goods g','d.goods_id=g.id','left')
                ->where($whereDetail)
                ->select();
            foreach ($order_detail as &$v) {
                $v['pics'] = unserialize($v['pics']);
                $v['cover'] = $v['pics'][0];
            }
            $info['order_detail'] = $order_detail;
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$info);
        $this->assign('qiniu_weburl',config('qiniu_weburl'));

        return $this->fetch();
    }

    //订单修改
    public function orderModPost() {

    }

    //退款
    public function orderRefund() {
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $where = [
                ['id','=',$val['id']],
                ['refund_apply','=',1],
                ['status','in',[1,2,3]]
            ];
            $exist = Db::table('mp_order')->where($where)->find();
            if(!$exist) {
                return ajax('订单不存在或状态已改变',-1);
            }
            $pay_order_sn = $exist['pay_order_sn'];
//            $exist['pay_price'] = 0.01;
            $arr = [
                'appid' => $this->mp_config['appid'],
                'mch_id'=> $this->mp_config['mch_id'],
                'nonce_str'=>randomkeys(32),
                'sign_type'=>'MD5',
                'transaction_id'=> $exist['trans_id'],
                'out_trade_no'=> $pay_order_sn,
                'out_refund_no'=> 'r' . $pay_order_sn,
                'total_fee'=> floatval($exist['pay_price'])*100,
                'refund_fee'=> floatval($exist['pay_price'])*100,
                'refund_fee_type'=> 'CNY',
                'refund_desc'=> '商品无货',
                'notify_url'=> $_SERVER['REQUEST_SCHEME'] . '://'.$_SERVER['HTTP_HOST'].'/wxRefundNotify',
                'refund_account' => 'REFUND_SOURCE_UNSETTLED_FUNDS'
            ];

            $arr['sign'] = getSign($arr);
            $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
            $res = curl_post_data($url,array2xml($arr),true);

            $result = xml2array($res);

            if($result && $result['return_code'] == 'SUCCESS') {
                if($result['result_code'] == 'SUCCESS') {
                    $update_data = [
                        'refund_apply' => 2,
                        'refund_time' => time()
                    ];
                    Db::table('mp_order')->where($where)->update($update_data);
                    //todo 库存销量修改
                    $whereDetail = [
                        ['order_id','=',$val['id']]
                    ];
                    $order_detail = Db::table('mp_order_detail')->where($whereDetail)->field('goods_id,num,attr_id')->select();
                    foreach ($order_detail as $v) {
                        if($v['attr_id']) {
                            Db::table('mp_goods_attr')->where('id','=',$v['attr_id'])->setInc('stock',$v['num']);
                        }
                        Db::table('mp_goods')->where('id','=',$v['goods_id'])->setInc('stock',$v['num']);
                        Db::table('mp_goods')->where('id','=',$v['goods_id'])->setDec('sales',$v['num']);
                    }
                    return ajax();
                }else {
                    return ajax($result['err_code_des'],-1);
                }
            }else {
                return ajax('退款通知失败',-1);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
    }

    //删除订单
    public function orderDel() {

    }

    public function modReceiver() {
        $val['receiver'] = input('post.receiver');
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $where = [
                ['id','=',$val['id']]
            ];
            $exist = Db::table('mp_order')->where($where)->find();
            if(!$exist) {
                return ajax('订单不存在或状态已改变',-1);
            }
            if($exist['refund_apply'] == 2 || $exist['status'] == 3) {
                return ajax('订单已结束,无法修改',-1);
            }
            if($exist['status'] == 2) {
                return ajax('订单已发货,无法修改',-1);
            }
            $update_data = [
                'receiver' => $val['receiver']
            ];
            Db::table('mp_order')->where($where)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }

    public function modTel() {
        $val['tel'] = input('post.tel');
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $where = [
                ['id','=',$val['id']]
            ];
            $exist = Db::table('mp_order')->where($where)->find();
            if(!$exist) {
                return ajax('订单不存在或状态已改变',-1);
            }
            if($exist['refund_apply'] == 2 || $exist['status'] == 3) {
                return ajax('订单已结束,无法修改',-1);
            }
            if($exist['status'] == 2) {
                return ajax('订单已发货,无法修改',-1);
            }
            $update_data = [
                'tel' => $val['tel']
            ];
            Db::table('mp_order')->where($where)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }

    public function modAddress() {
        $val['address'] = input('post.address');
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $where = [
                ['id','=',$val['id']]
            ];
            $exist = Db::table('mp_order')->where($where)->find();
            if(!$exist) {
                return ajax('订单不存在或状态已改变',-1);
            }
            if($exist['refund_apply'] == 2 || $exist['status'] == 3) {
                return ajax('订单已结束,无法修改',-1);
            }
            if($exist['status'] == 2) {
                return ajax('订单已发货,无法修改',-1);
            }
            $update_data = [
                'address' => $val['address']
            ];
            Db::table('mp_order')->where($where)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }

    public function modPrice() {
        $val['pay_price'] = input('post.pay_price');
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $where = [
                ['id','=',$val['id']]
            ];
            $exist = Db::table('mp_order')->where($where)->find();
            if(!$exist) {
                return ajax('订单不存在或状态已改变',-1);
            }
            if($exist['status'] != 0) {
                return ajax('订单已支付,无法修改金额',-1);
            }
            $update_data = [
                'pay_price' => $val['pay_price'],
                'pay_order_sn' => create_unique_number('')
            ];
            Db::table('mp_order')->where($where)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }


    public function commentList() {
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $where = [];
        if($param['search']) {
            $where[] = ['c.comment','like',"%{$param['search']}%"];
        }

        try {
            $count = Db::table('mp_goods_comment')->alias('c')->where($where)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_goods_comment')->alias('c')
                ->join('mp_user u','c.uid=u.id','left')
                ->join('mp_goods g','c.goods_id=g.id','left')
                ->where($where)
                ->order(['c.id'=>'DESC'])
                ->field('c.*,g.name,u.nickname,u.avatar')
                ->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('param',$param);
        return $this->fetch();
    }

    public function comment() {
        $param['goods_id'] = input('param.goods_id');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $where = [
            ['c.goods_id','=',$param['goods_id']]
        ];
        try {
            $count = Db::table('mp_goods_comment')->alias('c')->where($where)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_goods_comment')->alias('c')
                ->join('mp_user u','c.uid=u.id','left')
                ->where($where)
                ->order(['c.id'=>'DESC'])
                ->field('c.*,u.nickname,u.avatar')
                ->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('param',$param);
        return $this->fetch();
    }

    //删除资讯
    public function commentDel() {
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $exist = Db::table('mp_goods_comment')->where('id','=',$val['id'])->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_goods_comment')->where('id','=',$val['id'])->delete();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }

    //停用新闻
    public function commentHide()
    {
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $exist = Db::table('mp_goods_comment')->where('id','=',$val['id'])->find();
            if (!$exist) {
                return ajax('非法操作', -1);
            }
            Db::table('mp_goods_comment')->where('id','=',$val['id'])->update(['status' => 0]);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //启用新闻
    public function commentShow() {
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $exist = Db::table('mp_goods_comment')->where('id','=',$val['id'])->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_goods_comment')->where('id','=',$val['id'])->update(['status'=>1]);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }



}