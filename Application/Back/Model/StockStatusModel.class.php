<?php
namespace Back\Model;
use Think\Model;

/**
 * Class StockStatusModel
 * 后台库存状态模型
 * @package Back\Model
 */
class StockStatusModel extends Model
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