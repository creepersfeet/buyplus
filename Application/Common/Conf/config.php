<?php
return array(
	//'配置项'=>'配置值'
    'ACTION_SUFFIX' => 'Action', // 动作后缀

    'LOAD_EXT_CONFIG'  => 'mysql-server',// 载入额外的配置

    // 授权认证的配置
    'NON_AUTH_ACTION' => ['back/admin/login', '...'],
    'ALLOW_ACTION' => ['back/manage/dashboard', '...'],

    'DEFAULT_FILTER' => '', // 禁用通用的过滤器
);