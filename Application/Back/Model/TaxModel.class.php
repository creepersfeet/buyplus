<?php
namespace Back\Model;
use Think\Model;

/**
 * Class TaxModel
 * 后台税类型模型
 * @package Back\Model
 */
class TaxModel extends Model
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