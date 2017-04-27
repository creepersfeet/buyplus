<?php
namespace Back\Model;
use Think\Model;

/**
 * Class LengthUnitModel
 * 后台长度单位模型
 * @package Back\Model
 */
class LengthUnitModel extends Model
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