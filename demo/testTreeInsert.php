<?php
use wstree\Tree;
require '../vendor/autoload.php';
$tree=new Tree();

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
shuffle($data);
$tree->init($data);

p('','以下是关于两个insert方法的使用 （落位弱区的算法）');

/**
 * 查询可插入的节点信息(公排|弱区)
 * 通常作为插入用户时获取新用户的父节点
 * @param  Node $node 节点的实例
 * @param  boolean $natural 默认：false 是否自然排序(公排 从上到下 左到右) 默认为平衡排列(优选弱区)
 * @return array      [node,position]
 */
$result=$tree->insertable($tree['root'],false);
p($result['value'],'insertable() 返回可插入节点的父节点，只需判断left、right是否存在插入子节点即可');

/**
 * 插入新节点(公排|弱区)
 * @param  mixed $key  插入的值
 * @param  Node $node  节点的实例
 * @param  boolean $natural 默认：false 是否自然排序(公排 从上到下 左到右) 默认为平衡排列(优选弱区)
 * @return Node
 */
$result=$tree->insert('这是新增的节点',$tree['root']);
p($result['value'],'insert() 返回新增的节点，这只是在insertable()方法的基础上进行自我判断并插入操作');
