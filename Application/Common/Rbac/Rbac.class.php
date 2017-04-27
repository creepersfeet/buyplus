<?php

namespace Common\Rbac;


class Rbac
{
    /**
     * 获取用户具有的权限
     * @param $user_id 用户id
     * @return array 含有全部权限的数组
     */
    public function getActionList($user_id)
    {
        // 构思: 返回数据
//        ['back/brand/list', 'back/brand/add']
        $model = M('RoleAdmin');
        $rows = $model
            ->field('a.node action, c.node controller, m.node module')
            ->alias('ra')
            ->join('join __ROLE_ACTION__ rac On ra.role_id=rac.role_id')
            ->join('left join __ACTION__ a ON rac.action_id=a.id') // 动作
            ->join('left join __ACTION__ c ON a.parent_id=c.id') // 控制器
            ->join('left join __ACTION__ m ON c.parent_id=m.id') // 模块
            ->where(['ra.admin_id'=>$user_id])
            ->select()
            ;
        return array_map(function($row){
            return $row['module'] . '/' . $row['controller'] . '/' . $row['action'];
        }, $rows);
    }

    /**
     * 检测当前用户是否具有权限
     */
    public function checkAccess()
    {

        // 获取当前的 路由:
        $route = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
        $route = strtolower($route);

        // 判断是否需要认证
        if (in_array($route, C('NON_AUTH_ACTION'))) {
            // 不需要认证即可执行
            return true;
        }

        // 是否认证(是否登录)
        if (!$admin = session('admin')) {
            // 没有登录认证
            redirect(U('Back/Admin/login'));
        }

        // 是否为超级管理员
        $count = M('RoleAdmin')
            ->alias('ra')
            ->join('left join __ROLE__ r On ra.role_id=r.id')
            ->where(['r.is_super'=>'1', 'ra.admin_id'=>$admin['id']])
            ->count();
        if ($count > 0) {
            // 是超级管理员
            return true;// 继续执行
        }

        // 动作是否需要授权
        if (in_array($route, C('ALLOW_ACTION'))) {
            // 不需要
            return true;
        }

        // 判断是否具有权限
        $action_list = $this->getActionList($admin['id']);
        if (in_array($route, $action_list)) {
            return true;// 执行
        }
        // 没有授权
        redirect(U('Back/Manage/dashboard'));
    }

}