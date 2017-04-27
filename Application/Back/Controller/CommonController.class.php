<?php

namespace Back\Controller;
use Common\Rbac\Rbac;
use Think\Controller;

class CommonController extends Controller
{
    /**
     * 用于完成初始化的方法
     */
    public function _initialize()
    {
        $rbac = new Rbac();
        $rbac->checkAccess();

    }

}