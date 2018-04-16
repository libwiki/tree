<?php
use tree\Tree;
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
$tree->init($data);

$isComplete=$tree->isComplete($tree['root'])?'是':'否';
$isFull=$tree->isFull($tree['root'])?'是':'否';
p('','isComplete() 是否完全二叉树： '.$isComplete,1);
p('','isFull() 是否满二叉树： '.$isFull,1);

p('','getDepth() 获取深度为： '.$tree->getDepth($tree['root']['left']),1);
p('','getHeight() 获取当前节点高度(最高的那一边)为：  '.$tree->getHeight($tree['root']),1);
p('','getMinHeight() 获取当前节点高度(最低的那一边)为： '.$tree->getMinHeight($tree['root']));

echo '<br>下列中$item 是 Node 的实例<br>';
echo "<br>先序遍历演示()：";
$count=null;
$tree->preOrder($tree['root'],function($item)use(&$count){
    echo '<br>ItemValue：'.$item['value']['name'];
    $count++;
});
p('','$count 是演示一些额外的扩展 这里是统计树的总节点为： '.$count);


echo "<br>中序遍历演示：";
$tree->inOrder($tree['root'],function($item){
    echo '<br>ItemValue：'.$item['value']['name'];
});
p('');

echo "<br>后序遍历演示：";
$tree->preOrder($tree['root'],function($item)use(&$count){
    echo '<br>ItemValue：'.$item['value']['name'];
});
p('');

echo '<br>上下左右遍历(层层遍历)演示：（这里的参数与另三个遍历参数有区别主要为了应用于$tree->isComplete()方法中）';

$tree->levelOrder($tree['root'],function($item)use(&$count){
    echo '<br>ItemValue：'.$item['value']['name'];
});
