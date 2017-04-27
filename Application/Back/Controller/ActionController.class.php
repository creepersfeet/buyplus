<?php
namespace Back\Controller;
use Think\Page;

/**
 * Class ActionController
 * 动作管理
 * @package Back\Controller
 */
class ActionController extends CommonController
{
    /**
     * 添加
     */
    public function addAction()
    {
        if (IS_POST) {
            // 校验, 入库
            $model = D('Action');
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
        $model = M('Action');

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
                M('Action')->where(['id'=>['in', $pk_list]])->delete();
                $this->redirect('list');
                break;
        }
    }


    /**
     * 编辑
     * @param $id 正在编辑的动作主键
     */
    public function editAction($id)
    {
        $model = D('Action');
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
}