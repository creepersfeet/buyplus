<?php
/**
 * Created by 小韩说理
 * User: 韩忠康
 * Date: 2017/4/20
 * Time: 10:58
 */

namespace Back\Controller;

use Common\Rbac\Rbac;

class ManageController extends CommonController
{

    public function dashboardAction()
    {
        echo '管理面板';
//        $rbac = new Rbac();
//        dump($rbac->getActionList(3));
//        die;
    }

}