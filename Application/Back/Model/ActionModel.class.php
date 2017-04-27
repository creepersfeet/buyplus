<?php
namespace Back\Model;
use Think\Model;

/**
 * Class ActionModel
 * 后台动作模型
 * @package Back\Model
 */
class ActionModel extends Model
{
    // 批量验证
    protected $patchValidate = true;

    // 验证规则
    protected $_validate = [
    ];

//    填充数据
    protected $_auto = [
    ];

    /**
     *
     */
    //    示例的返回数据
//        [
//            ['id'=>1, 'title'=>'后台', 'node'=>'back', 'children'=>[
//                    ['id'=>4, 'title'=>'品牌', 'node'=>'brand', 'children' => [
//                            ['id'=>6, 'title'=>'添加', 'node'=>'add'],
//                            ['id'=>6, 'title'=>'编辑', 'node'=>'edit'],
//                            ['id'=>6, 'title'=>'批量删除', 'node'=>'multi'],
//                            ['id'=>6, 'title'=>'列表', 'node'=>'list'],
//                        ]
//                    ],
//                    ['id'=>5, 'title'=>'分类', 'node'=>'category'],
//                ]
//            ],
//            ['id'=>1, 'title'=>'前台', 'node'=>'home'],
//        ];
    public function getNested()
    {
        // 获取全部的action
        $list = $this->select();
        // 递归处理嵌套结构
        return $this->nested($list);
    }

    protected function nested($rows, $id=0)
    {
        $children = [];
        // 遍历全部的元素
        foreach($rows as $row) {
            // 判断是否为子元素
            if ($row['parent_id'] == $id) {
                // 找到其后代元素
                // 将结果放入当前元素的children子元素中
                $row['children'] = $this->nested($rows, $row['id']);
                // 记录下载当前的后代元素.
                $children[] = $row;
            }
        }
        // 返回全部的结果即可
        return $children;
    }

}