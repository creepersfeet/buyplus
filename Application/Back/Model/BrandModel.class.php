<?php

namespace Back\Model;


use Think\Model;

/**
 * Class BrandModel
 * 后台品牌模型
 * @package Back\Model
 */
class BrandModel extends Model
{
    // 批量验证
    protected $patchValidate = true;

    // 验证规则
    protected $_validate = [
//        ['title', 'require', '名称必须'], //self::EXISTS_VALIDATE, '', self::MODEL_BOTH],
        // 名称不能重复, 必须
        ['title', 'require', '名称必须', self::EXISTS_VALIDATE, '', self::MODEL_BOTH],
        ['title', '', '名称已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH],
//        官网, 必须要是网址
        ['site', 'url', 'URL地址不正确', self::VALUE_VALIDATE],
//        排序, 使用整数
        ['sort_number', 'number', '请使用数值排序'],
    ];

//    填充数据
    protected $_auto = [
//        创建时间, 仅仅在插入时需要使用当前时间填充
        ['created_at', 'time', self::MODEL_INSERT, 'function'],
//        更新时间, 插入和更新是需要使用当前时间填充
        ['updated_at', 'time', self::MODEL_BOTH, 'function'],
    ];
}