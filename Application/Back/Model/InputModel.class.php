<?php
namespace Back\Model;
use Think\Model;

/**
 * Class InputModel
 * 后台输入类型模型
 * @package Back\Model
 */
class InputModel extends Model
{
    // 批量验证
    protected $patchValidate = true;

    // 验证规则
    protected $_validate = [
    ];

//    填充数据
    protected $_auto = [
    ];
}