<?php
namespace Back\Model;
use Think\Model;

/**
 * Class CategoryModel
 * 后台分类模型
 * @package Back\Model
 */
class CategoryModel extends Model
{
    // 批量验证
    protected $patchValidate = true;

    // 验证规则
    protected $_validate = [
    ];

//    填充数据
    protected $_auto = [
    ];

    public function getTree()
    {
        $list = $this->select();
        return $this->tree($list);
    }
    protected  function tree($list, $category_id=0, $level=0)
    {
        static $tree = [];
        foreach($list as $row) {
            if ($row['parent_id'] == $category_id) {
                $row['level'] = $level;
                $tree[] = $row;
                $this->tree($list, $row['id'], $level+1);
            }
        }
        return $tree;
    }
}