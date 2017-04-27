<?php
namespace Back\Model;
use Think\Model;

/**
 * Class RoleModel
 * 后台角色模型
 * @package Back\Model
 */
class RoleModel extends Model
{
    // 批量验证
    protected $patchValidate = true;

    // 验证规则
    protected $_validate = [
        ['title', 'require', '角色必须', self::EXISTS_VALIDATE, '', self::MODEL_BOTH],
        ['title', '', '角色已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH],
    ];

//    填充数据
    protected $_auto = [
        //        创建时间, 仅仅在插入时需要使用当前时间填充
        ['created_at', 'time', self::MODEL_INSERT, 'function'],
//        更新时间, 插入和更新是需要使用当前时间填充
        ['updated_at', 'time', self::MODEL_BOTH, 'function'],
    ];
}