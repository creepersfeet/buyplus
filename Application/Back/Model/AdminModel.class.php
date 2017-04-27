<?php
namespace Back\Model;
use Think\Model;

/**
 * Class AdminModel
 * 后台管理员模型
 * @package Back\Model
 */
class AdminModel extends Model
{
    // 批量验证
    protected $patchValidate = true;

    // 验证规则
    protected $_validate = [
    ];

//    填充数据
    protected $_auto = [
        //        创建时间, 仅仅在插入时需要使用当前时间填充
        ['created_at', 'time', self::MODEL_INSERT, 'function'],
//        更新时间, 插入和更新是需要使用当前时间填充
        ['updated_at', 'time', self::MODEL_BOTH, 'function'],
        ['salt', 'mkSalt', self::MODEL_INSERT, 'callback'],
        ['password', 'mkPassword', self::MODEL_INSERT, 'callback'],
    ];
    private $new_salt;
    protected function mkSalt()
    {
        return $this->new_salt = substr(sha1(time()), 0, 4);
    }
    protected  function mkPassword($password)
    {
        return md5($password . $this->new_salt);
    }
}