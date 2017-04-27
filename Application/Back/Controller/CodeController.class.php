<?php

namespace Back\Controller;


use Think\Controller;

class CodeController extends Controller
{
    /**
     * 配置表单
     */
    public function setAction()
    {
        $this->display();
    }

    /**
     * 替换生成
     */
    public function generateAction()
    {
        // 一, 获取需要替换的数据
        // 1, 获取表名
        $table = I('post.table', '', 'trim');

        // 2, 获取模型和控制器名
        // 下划线分割explode, 每个单词首字母大写array_map+ucfirst, 连接implode
        $model = $controller = implode(array_map('ucfirst', explode('_', $table)));

        // 3, 表注释%name%
        $db = C('DB_NAME');
        $prefix = C('DB_PREFIX');
        $sql = "SELECT `TABLE_COMMENT` FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA`='$db' AND  `TABLE_NAME` = '$prefix$table' limit 1";
        $rows = M()->query($sql);// 永远返回2维数组
        $name = $rows[0]['table_comment'];

        // 4, 主键, 字段信息
        $sql = "SELECT `COLUMN_NAME`, `COLUMN_KEY`, `COLUMN_COMMENT` FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA`='$db' AND  `TABLE_NAME` = '$prefix$table'";
        $rows = M()->query($sql);
        foreach($rows as $row) {
            $columns[$row['column_name']] = ['name' => $row['column_name'], 'comment' => $row['column_comment']];

            if ($row['column_key'] == 'PRI') {
                $pk_field = $row['column_name'];
            }
        }

        // 二, 替换控制器
        // 1, 查找
        $search = ['%CONTROLLER%', '%MODEL%', '%NAME%', '%PK_FIELD%'];
        // 2, 替换内容
        $replace = [$controller, $model, $name, $pk_field];
        // 3, 模板字符串
        $template = file_get_contents(APP_PATH . 'Back/CodeTemplate/controller.template');
        // 4, 替换生成内容
        $content = str_replace($search, $replace, $template);

        // 三, 生成控制器代码文件
        $file = APP_PATH . 'Back/Controller/' . $controller . 'Controller.class.php';
        file_put_contents($file, $content);
        echo '控制器: ', $file, '创建成功', '<br>';

        // 四, 替换生成模型代码文件
        // 1, 查找
        $search = ['%MODEL%', '%NAME%',];
        // 2, 替换内容
        $replace = [$model, $name];
        // 3, 模板字符串
        $template = file_get_contents(APP_PATH . 'Back/CodeTemplate/model.template');
        // 4, 替换生成内容
        $content = str_replace($search, $replace, $template);
        // 5, 生成代码文件
        $file = APP_PATH . 'Back/Model/' . $model . 'Model.class.php';
        file_put_contents($file, $content);
        echo '模型: ', $file, '创建成功', '<br>';

        // 五, 生成列表模板
        // 1, 遍历全部字段, 拼凑表头和数据单元格
        $th_list = '';
        $td_list = '';
        $list_th_template = file_get_contents(APP_PATH . 'Back/CodeTemplate/list.th.template');
        $list_th_order_template = file_get_contents(APP_PATH . 'Back/CodeTemplate/list.th.order.template');
        $list_td_template = file_get_contents(APP_PATH . 'Back/CodeTemplate/list.td.template');
        foreach(I('post.fields') as $field=>$info) {
            // 判断当前字段是否需要在列表中展示
            if (isset($info['is_list'])) {
                // 需要在列表中展示
                // 拼凑表头
                $search = ['%FIELD%', '%FIELD_COMMENT%'];
                $replace = [$field, $info['comment']];
                if(isset($info['is_order'])) {
                    // 使用排序模板
                    $template = $list_th_order_template;
                } else {
                    // 使用未排序模板
                    $template = $list_th_template;
                }
                $th_list .= str_replace($search, $replace, $template);

                // 拼凑数据单元格
                $search = ['%FIELD%'];
                $replace = [$field];
                $td_list .= str_replace($search, $replace, $list_td_template);
            }
        }
        // 2, 拼凑list整体模板
        $search = ['%NAME%', '%TH_LIST%', '%TD_LIST%', '%PK_FIELD%'];
        $replace = [$name, $th_list, $td_list, $pk_field];
        $template = file_get_contents(APP_PATH . 'Back/CodeTemplate/list.template');
        $content = str_replace($search, $replace, $template);
        // 3, 生成文件
//        创建视图目录
        $path = APP_PATH . 'Back/View/' . $controller . '/';
        if (!is_dir($path)) {
            mkdir($path);
        }
        $file = $path . 'list.html';
        file_put_contents($file, $content);
        echo '列表模板:', $file, '创建成功', '<br>';

        // 六, 替换生成add模板
        // 1, 字段子模板
        $field_list = '';
        $add_field_template = file_get_contents(APP_PATH . 'Back/CodeTemplate/add.field.template');
        foreach(I('post.fields') as $field=>$info) {
            // 判断当前字段是否需要在添加中展示
            if (isset($info['is_add'])) {
                $search = ['%FIELD%', '%FIELD_COMMENT%'];
                $replace = [$field, $info['comment']];
                $field_list .= str_replace($search, $replace, $add_field_template);
            }
        }
        // 2, 整体添加模板
        $search = ['%NAME%', '%FIELD_LIST%'];
        $replace = [$name, $field_list];
        $template = file_get_contents(APP_PATH . 'Back/CodeTemplate/add.template');
        $content = str_replace($search, $replace, $template);
        $file = $path . 'add.html';
        file_put_contents($file, $content);
        echo '添加模板:', $file, '创建成功', '<br>';


        // 七, 替换生成edit模板
        // 1, 字段子模板
        $field_list = '';
        $edit_field_template = file_get_contents(APP_PATH . 'Back/CodeTemplate/edit.field.template');
        foreach(I('post.fields') as $field=>$info) {
            // 判断当前字段是否需要在添加中展示
            if (isset($info['is_edit'])) {
                $search = ['%FIELD%', '%FIELD_COMMENT%'];
                $replace = [$field, $info['comment']];
                $field_list .= str_replace($search, $replace, $edit_field_template);
            }
        }
        // 2, 整体edit模板
        $search = ['%NAME%', '%FIELD_LIST%', '%PK_FIELD%'];
        $replace = [$name, $field_list, $pk_field];
        $template = file_get_contents(APP_PATH . 'Back/CodeTemplate/edit.template');
        $content = str_replace($search, $replace, $template);
        $file = $path . 'edit.html';
        file_put_contents($file, $content);
        echo '编辑模板:', $file, '创建成功', '<br>';

    }


    /**
     * 获取字段信息
     */
    public function fieldAction()
    {
        $table = I('request.table', null, 'trim');

        // 表描述
        $db = C('DB_NAME');
        $prefix = C('DB_PREFIX');
        $sql = "SELECT `TABLE_COMMENT` FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA`='$db' AND  `TABLE_NAME` = '$prefix$table' limit 1";
        $rows = M()->query($sql);// 永远返回2维数组
        $comment = $rows[0]['table_comment'];

        // 字段信息
        $sql = "SELECT `COLUMN_NAME`, `COLUMN_KEY`, `COLUMN_COMMENT` FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA`='$db' AND  `TABLE_NAME` = '$prefix$table'";
        $rows = M()->query($sql);
        foreach($rows as $row) {
            $columns[$row['column_name']] = ['name' => $row['column_name'], 'comment' => $row['column_comment']];

            if ($row['column_key'] == 'PRI') {
                $columns[$row['column_name']]['is_pk'] = true;
            } else {
                $columns[$row['column_name']]['is_pk'] = false;
            }
        }

        $this->ajaxReturn([
            'fields' => $columns, // 字段信息列表
            'comment' =>  $comment, // 表的描述
        ]);// 自动将数组转换为json, 返回
    }

}