<?php


function grantCond($route)
{
//    是否是超管
    $count = M('RoleAdmin')
        ->alias('ra')
        ->join('left join __ROLE__ r On ra.role_id=r.id')
        ->where(['r.is_super'=>'1', 'ra.admin_id'=>session('admin.id')])
        ->count();
    if ($count > 0) {
        // 是超级管理员
        return true;// 继续执行
    }
//    是否在授权列表
    $rbac = new \Common\Rbac\Rbac();
    return in_array($route, $rbac->getActionList(session('admin.id')));
}