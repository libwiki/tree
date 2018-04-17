<?php
use wstree\Tree;
require '../vendor/autoload.php';
$data=[
    ['id'=>1,'name'=>'A','pid'=>0],
    ['id'=>2,'name'=>'B','pid'=>1],
    ['id'=>3,'name'=>'C','pid'=>1],
    ['id'=>4,'name'=>'D','pid'=>3],
    ['id'=>5,'name'=>'E','pid'=>3],
    ['id'=>6,'name'=>'F','pid'=>5],
    ['id'=>7,'name'=>'G','pid'=>4],
    ['id'=>8,'name'=>'H','pid'=>4],
];


p('','以下演示Tree类提供的两个排序辅助方法');

/**
 * 从上到下 从左到右的排序(层层排序)
 * @param  array   $data 用户数据
 * @param  integer $col  列数（二叉树、三叉树、四叉树。。。）
 * @param  integer $pid  起始pid
 * @param  string  $pidKey  父级ID 键名
 * @return array
 */
shuffle($data); // 打乱数组
$result=Tree::levelSort($data,0,'pid');
/**
 * 子集个数统计（直属下级）(会增加数组字段‘_childrens’)
 * @param  array   $arr     统计数据
 * @param  string  $pidKey  父级ID 键名
 * @return array
 */
$result=Tree::_childrens($data,'pid');
p($result,'_childrens()、levelSort() 演示 该方法在 Tree类中实际并未使用');



/**
 * 先序 排序 以及是否存在子集判断（增加_level字段 深度 0 起始。 getDepth()方法为 1 起始）
 * @param  array   $array 用户数据
 * @param  array   $result 引用类型最终返回的数据
 * @param  integer $pid  起始pid
 * @param  boolean  $sort   是否先进行按主键值的排序
 * @param  integer  $level  级别默认为0 不建议传递
 * @param  string   $pidKey  父级ID 键名
 * @return array
 * $options=[$pid=0,$sort=true,$level=0,$pidKey='pid'];
 */
shuffle($data); // 打乱数组
$result=[];
$options=['pid'=>0,'sort'=>true,'level'=>0,'pidKey'=>'pid'];
Tree::preSort($data,$result,$options=[]);

p($result,'levelSort 演示 该方法在 Tree类init()方法中使用');
