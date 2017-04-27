<?php
namespace Back\Controller;

use Think\Page;
use Think\Upload;
use Think\Image;

/**
 * Class GoodsController
 * 商品管理
 * @package Back\Controller
 */
class GoodsController extends CommonController
{
    /**
     * 添加
     */
    public function addAction()
    {
        if (IS_POST) {
            // 校验, 入库
            $model = D('Goods');
            if ($model->create()) {
                // 验证通过
                $id = $model->add();
                if ($id) {
                    // 数据被插入到数据表
                    $this->redirect('list'); // 重定向
                }
            }
            // 无论任何原因失败, 都会重定向到添加页面
            // 携带错误消息和错误数据
            session('message', $model->getError()); // 错误消息
            session('data', I('post.')); // 错误数据
            $this->redirect('add');
        } else {
            // 当前非post, 就是get

            // 处理错误消息, 错误数据
            $message = session('message') ? session('message') : [];
            session('message', null);// 清空该session
            $this->assign('message', $message);
            $data = session('data') ? session('data') : [];
            session('data', null);
            $this->assign('data', $data);

            // 分配数据
            $this->assign('stock_unit_list', M('StockUnit')->select());
            $this->assign('stock_status_list', M('StockStatus')->select());
            $this->assign('length_unit_list', M('LengthUnit')->select());
            $this->assign('weight_unit_list', M('WeightUnit')->select());
            $this->assign('tax_list', M('Tax')->select());

            $this->assign('brand_list', M('Brand')->select());

            // 展示模板
            $this->display();
        }
    }

    /**
     * 列表
     */
    public function listAction()
    {
//        获取模型
        $model = M('Goods');

//        确定条件
        $filter = [];// 默认没有过滤条件
        $filter_title = I('get.filter_title', null);
        // 反显搜索词
        $this->assign('filter_title', $filter_title);
        if (!is_null($filter_title)) {
            // 用户有传递 名称过滤条件
            $filter['title'] = ['like', $filter_title . '%'];
        }

//        分页相关
//        获取符合条件的总记录数
        $total = $model
            ->where($filter)
            ->count();
//        每页的记录数
        $limit =  10;
        $page = new Page($total, $limit); // use Think\Page;
        // 定制选项
        $page->setConfig('prev', '&lt;');
        $page->setConfig('next', '&gt;');
        $page->setConfig('first', '|&lt;');
        $page->setConfig('last', '&gt;|');
        $page->setConfig('header', "显示从 第%PAGE_FIRST% 到 第%PAGE_LAST% 条 , 共 %TOTAL_ROW% 条 （总 %TOTAL_PAGE% 页）");
        $page->setConfig('theme', "<div class=\"col-sm-6 text-left\"><ul class=\"pagination\">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%</ul></div><div class=\"col-sm-6 text-right\">%HEADER%</div>");
        $this->assign('page_html', $page->show());


//        排序定制
        $order = [];
        $order_field = I('get.order_field', null);
        $order_type = I('get.order_type', null);
        if (!is_null($order_field) && !is_null($order_type)) {
            // 形成排序数据 传递给模型
            $order[$order_field] = $order_type;
            // 分配到模板, 当前的排序参数, 为了生成新的排序链接
            $this->assign('order', ['order_field'=>$order_field, 'order_type'=>$order_type]);
        }


//        完成查询
        $rows = $model
            ->where($filter)
            ->order($order)
            // 获取部分数据
            ->limit($page->firstRow . ', ' . $limit)
            ->select();
        $this->assign('rows', $rows);

        // 展示
        $this->display();
    }

    /**
     * 批量处理
     */
    public function multiAction()
    {
        // 获取批量操作的ID列表
        $pk_list = I('post.selected', null);
        if (is_null($pk_list)) {
            // 不需要执行任何操作
            $this->redirect('list');
        }
        // 执行批量操作
        // 除了删除外, 支持多种批量操作, 默认的操作是删除
        $operate = I('post.operate', 'delete');
        switch($operate) {
            case 'delete':
                M('Goods')->where(['id'=>['in', $pk_list]])->delete();
                $this->redirect('list');
                break;
        }
    }


    /**
     * 编辑
     * @param $id 正在编辑的商品主键
     */
    public function editAction($id)
    {
        $model = D('Goods');
//        判断当前请求方法
        if (IS_POST) {
            $data = I('post.');
            $data['id'] = $id;
            if ( $model->create($data) && $model->save()) { // POST数据中没有ID, 需要自己将ID传递进入
                // 验证通过, 并update 成
                $this->redirect('list');
            }

            // 发生了错误
            // 无论任何原因失败, 都会重定向到添加页面
            // 携带错误消息和错误数据
            session('message', $model->getError()); // 错误消息
            session('data', $data); // 错误数据
            $this->redirect('edit', ['id'=>$id]);

        } else {

            // get请求, 显示表单
            // 获取错误消息和数据
            $message = session('message') ? session('message') : [];
            session('message', null);// 清空该session
            $this->assign('message', $message);
            $data = session('data') ? session('data') : [];
            session('data', null);
            $this->assign('data', $data);
            // 如果没有错误数据 则从数据库中获取需要编辑的内容
            if (empty($data)) {
                $row = $model->find($id);
                $this->assign('data', $row);
            }
            $this->display();
        }
    }

    /**
     * ajax上传分类图像
     */
    public function uploadAction()
    {
        // 初始化配置上传工具类
        $upload = new Upload(); // use Think\Upload;
        $upload->maxSize = 1*1024*1024;// 1M
        $upload->exts = ['jpeg', 'jpg', 'gif', 'png'];
        $upload->rootPath = './Upload/';
        $upload->savePath = 'Goods/';// 子目录

        $info = $upload->uploadOne($_FILES['image']);

        if($info) {
            // 上传成功
            switch(I('get.type', 'goods')) {
                case 'goods':
                    // 制作缩略图
                    $image = new Image();
                    $image->open($upload->rootPath . $info['savepath'] . $info['savename']);
                    $width = 300;
                    $height = 340;
                    $image->thumb($width, $height, Image::IMAGE_THUMB_FILLED);
                    if (!is_dir ('./Public/Thumb/' . $info['savepath'])) {
                        mkdir ('./Public/Thumb/' . $info['savepath']);
                    }
                    $image->save('./Public/Thumb/' . $info['savepath'] . "thumb-{$width}x{$height}-" . $info['savename']);

                    $info['image_thumb'] = $info['savepath'] . "thumb-{$width}x{$height}-" . $info['savename'];
                    break;

                case 'image':
                    // 制作缩略图
                    $image = new Image();
                    $image->open($upload->rootPath . $info['savepath'] . $info['savename']);
                    // 大图
                    $width = 800;
                    $height = 800;
                    $image->thumb($width, $height, Image::IMAGE_THUMB_FILLED);
                    if (!is_dir ('./Public/Thumb/' . $info['savepath'])) {
                        mkdir ('./Public/Thumb/' . $info['savepath']);
                    }
                    $image->save('./Public/Thumb/' . $info['savepath'] . "big-" . $info['savename']);
                    $info['image_big'] = $info['savepath'] . "big-" . $info['savename'];
                    // 中
                    $width = 300;
                    $height = 300;
                    $image->thumb($width, $height, Image::IMAGE_THUMB_FILLED);
                    if (!is_dir ('./Public/Thumb/' . $info['savepath'])) {
                        mkdir ('./Public/Thumb/' . $info['savepath']);
                    }
                    $image->save('./Public/Thumb/' . $info['savepath'] . "medium-" . $info['savename']);
                    $info['image_medium'] = $info['savepath'] . "medium-" . $info['savename'];
                    // 小
                    $width = 60;
                    $height = 60;
                    $image->thumb($width, $height, Image::IMAGE_THUMB_FILLED);
                    if (!is_dir ('./Public/Thumb/' . $info['savepath'])) {
                        mkdir ('./Public/Thumb/' . $info['savepath']);
                    }
                    $image->save('./Public/Thumb/' . $info['savepath'] . "small-" . $info['savename']);
                    $info['image_small'] = $info['savepath'] . "small-" . $info['savename'];
                    $info['delete_url'] = U('imageDelete', ['goods_id'=>I('get.goods_id'), 'image'=>urlencode($info['savepath'].$info['savename']), 'image_small'=>urlencode($info['image_small']), 'image_medium'=>urlencode($info['image_medium']), 'image_big'=>urlencode($info['image_big'])]);
                    break;

            }

            $this->ajaxReturn($info);
        } else {
            $this->ajaxReturn(['error'=>1]);
        }
    }

    /**
     * 处理商品相册图像
     * @param int $id 商品id
     */
    public function imageAction($id)
    {
        if (IS_POST) {

            // 将图像信息入image表
            $image_rows = I('post.image', []);
            // 每行增加goods_id元素
            $image_rows = array_map(function($row) use ($id) {
                $row['goods_id'] = $id;
                return $row;
            }, $image_rows);
            // 批量插入
            M('Image')->addAll($image_rows, [], true); // replace 冲突替换 : replace into
            // 重定向该页面
            $this->redirect('image', ['id'=>$id]);
        } else {
            // 展示当前商品拥有的商品相册,
            // 添加的表单
            $this->assign('image_list', M('Image')->where(['goods_id'=>$id])->order('sort_number')->select());

            $this->display();
        }

    }

    public function imageDeleteAction()
    {
        if ($id = I('get.id', null)) {
            // 查询需要删除的文件
            $row = M('Image')->field('image, image_small, image_medium, image_big')->find($id);
            M('Image')->delete($id);
        }
        // 删除文件
        $image = isset($row['image']) ? $row['image'] : I('get.image');
        $image_small = isset($row['image_small']) ? $row['image_small'] : I('get.image_small');
        $image_medium = isset($row['image_medium']) ? $row['image_medium'] : I('get.image_medium');
        $image_big = isset($row['image_big']) ? $row['image_big'] : I('get.image_big');

        @unlink('./Upload/' . $image);
        @unlink('./Public/Thumb/' . $image_small);
        @unlink('./Public/Thumb/' . $image_big);
        @unlink('./Public/Thumb/' . $image_medium);

        $this->redirect('image', ['id'=>I('get.goods_id')]);
    }

    /**
     * 商品属性管理
     * @param $goods_id 商品ID
     */
    public function attributeAction($goods_id)
    {
        $goods = M('Goods');
        $goods->find($goods_id); // $row = $model->find($goods_id); $row['type_id']

        if (IS_POST) {
            // 更新属性
            // 更新商品类型
            // 判断类型是否更新, 如果更新, 则删除已有的属性商品关联
            if ($goods->type_id != I('post.type_id', 0)) {
                // 删除已有关联
                M('GoodsAttribute')->where(['goods_id'=>$goods_id])->delete();

                // 重写设置type_id
                $goods->type_id = I('post.type_id', 0); // AR模式, 活动记录模式, ActiveRecord
                $goods->save();
            }



            // 更新商品属性
//            $rows = array_map(function($row) use($goods_id) {
//                $row['goods_id'] = $goods_id;
//                return $row;
//            }, I('post.ga', []));
//            M('GoodsAttribute')->addAll($rows, [], true);
            // 每个属性独立处理, 判断是否为select-multi类型
            foreach(I('post.ga', []) as $ga) {
//                先插入 GoodsAttribute, 无论任何属性类型, 都需要插入
                if ($ga['id'] == '') {
                    // 执行添加
                    $ga_id = M('GoodsAttribute')->add([
                        'goods_id'=>$goods_id,
                        'attribute_id'=>$ga['attribute_id'],
                        'value' => isset($ga['value']) ? $ga['value'] : '',
                    ]);
                } else {
                    // 执行更新
                    M('GoodsAttribute')->save([
                        'id' => $ga['id'],
                        'goods_id'=>$goods_id,
                        'attribute_id'=>$ga['attribute_id'],
                        'value' => isset($ga['value']) ? $ga['value'] : '',
                    ]);
                    $ga_id = $ga['id'];
                }

                // 判断是否需要编辑 goods_attribute_option表中数据
                foreach($ga['option_id'] as $option_id) {
                    M('GoodsAttributeOption')->add([
                        'goods_attribute_id' => $ga_id,
                        'option_id' => $option_id,
                    ]);
                }
            }

            $this->redirect('attribute', ['goods_id'=>$goods_id]);

        } else {
            // 展示属性
            // 确定当前商品是否具有属性, 新加的商品没有属性
            // 确定商品属性类型, 如果有则查询对应的全部属性和值
            // 如果没有, 该商品还未指定任何类型属性.

            if ($goods->type_id != '0') { // AR模式, 活动记录模式, ActiveRecord
//              有类型, 获取当前类型下的全部属性, 及其商品对应该属性的值.
                $goods_attribute_list = M('Attribute')
                    ->field('a.title, a.id attribute_id, ga.value, ga.id, ga.attribute_id, i.key')
                    ->alias('a')
                    ->join('left join __GOODS_ATTRIBUTE__ ga ON a.id=ga.attribute_id')
                    ->join('left join __INPUT__ i ON i.id=a.input_id')
                    ->where(['a.type_id'=>$goods->type_id, 'ga.goods_id'=>$goods_id])
                    ->select();
                // 遍历全部的属性, 依次获取属性的选项列表
                foreach($goods_attribute_list as $key => $attr) {
                    // 获取该属性的对应的选项列表
                    if ($attr['key'] == 'select-multi') {
                        // 需要获取
                        $option_list = M('Option')
                            ->field('id, title')
                            ->where(['attribute_id'=>$attr['attribute_id']])
//                            ->fetchSql(true)
                            ->select()
                        ;
                        // 将Optionlist 选项列表, 加入到属性数据中.
                        $goods_attribute_list[$key]['option_list'] = $option_list;

                    }
                }
                $this->assign('goods_attribute_list', $goods_attribute_list);
            }
            $this->assign('goods', $goods);
            $this->assign('type_id', $goods->type_id);
            $this->assign('type_list', M('Type')->select());
            $this->display();
        }

    }

    public function attributeListAction()
    {
        $type_id = I('request.type_id', null);
        $rows = M('Attribute')
            ->alias('a')
            ->field('a.title, a.id, i.key')
            ->join('left join __INPUT__ i ON i.id=a.input_id')
            ->where(['type_id'=>$type_id])
            ->select();

        // 遍历全部的属性, 依次获取属性的选项列表
        foreach($rows as $key => $attr) {
            // 获取该属性的对应的选项列表
            if ($attr['key'] == 'select-multi') {
                // 需要获取
                $option_list = M('Option')
                    ->field('id, title')
                    ->where(['attribute_id'=>$attr['id']])
                    ->select();
                // 将Optionlist 选项列表, 加入到属性数据中.
                $rows[$key]['option_list'] = $option_list;
            }
        }

        $this->ajaxReturn($rows);
    }
}