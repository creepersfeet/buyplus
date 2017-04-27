<?php


/**
 * @param $route
 * @param array $param
 * @param $field
 * @param array $order_param, ['order_field’=>’title’, ‘order_type’=>’desc’]
 */
function UOrder($route, $param=[], $field, $order_param=[])
{
    // 判断要为当前字段生成何种链接地址
    // 增加了排序字段参数
    $param['order_field']  = $field;
    // 需要确定排序方式
//    当前的排序没有按照该字段: 升序
//    当前按照该字段降序排序: 升序
//    当前按照该字段升序排序: 降序
    $param['order_type'] = ($order_param['order_field']==$field && $order_param['order_type']=='asc') ? 'desc' : 'asc';

    return U($route, $param);
}

/**
 * @param $field
 * @param array $order_param
 * @return string
 */
function ClassOrder($field, $order_param=[])
{
//    '', 'asc', 'desc'
//        如果按照该字段排序, 再去排序方式, 选择返回asc或desd. 否则直接返回空字符串
        return $order_param['order_field'] == $field ? ($order_param['order_type']=='asc'? 'asc':'desc') : '';
}


function descriptionFilter($desc)
{
    // 仅仅找到script标记内容, 将其转成实体即可
//    其他标记不与处理
    // 通过正则查找替换完成
//    <script type="text/javascript">alert('xx')</script>
//    <b>nihao</b>
//    <script>alert('yy')</script>
    $pattern = '/<script.*?>.*?<\/script>/is'; // 单行, 忽略大小写
    return preg_replace_callback($pattern, function($match) {
        return htmlspecialchars($match[0]);
    }, $desc);
}