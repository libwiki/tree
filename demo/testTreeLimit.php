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
p('Tree 类存在两个属性 $root（根节点实例）、$current（当前指针节点实例）');
p($tree['root'],'这是通过一个多行数据生成二叉树的例子、结果与testTree生成的树是一样的');
