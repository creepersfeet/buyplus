<?php
namespace Back\Controller;

use Think\Image;
use Think\Page;
use Think\Upload;

/**
 * Class CategoryController
 * 分类管理
 * @package Back\Controller
 */
class CategoryController extends CommonController
{
    /**
     * 添加
     */
    public function addAction()
    {
        if (IS_POST) {
            // 校验, 入库
            $model = D('Category');
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

            // 获取全部的分类树
            $this->assign('category_list', D('Category')->getTree());

            // 展示模板
            $this->display();
        }
    }

    /**
     * 列表
     */
    public function listAction()
    {

        $this->assign('rows', D('Category')->getTree());
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
                M('Category')->where(['id'=>['in', $pk_list]])->delete();
                $this->redirect('list');
                break;
        }
    }


    /**
     * 编辑
     * @param $id 正在编辑的分类主键
     */
    public function editAction($id)
    {
        $model = D('Category');
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
//        dump($_FILES);
        // 初始化配置上传工具类
        $upload = new Upload(); // use Think\Upload;
        $upload->maxSize = 1*1024*1024;// 1M
        $upload->exts = ['jpeg', 'jpg', 'gif', 'png'];
        $upload->rootPath = './Upload/';
        $upload->savePath = 'Category/';// 子目录

        $info = $upload->uploadOne($_FILES['image']);

        if($info) {
            // 上传成功
            // 制作缩略图
            $image = new Image();
            $image->open($upload->rootPath . $info['savepath'] . $info['savename']);
            $image->thumb(150, 150, Image::IMAGE_THUMB_FILLED);
            if (!is_dir ('./Public/Thumb/' . $info['savepath'])) {
                mkdir ('./Public/Thumb/' . $info['savepath']);
            }
            $image->save('./Public/Thumb/' . $info['savepath'] . 'thumb-150x150-' . $info['savename']);

            $info['image_thumb'] = $info['savepath'] . 'thumb-150x150-' . $info['savename'];
            $this->ajaxReturn($info);
        } else {
            $this->ajaxReturn(['error'=>1]);
        }
    }
}