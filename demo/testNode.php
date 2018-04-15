<?php
use tree\Node;
require '../vendor/autoload.php';
p('Node 类仅做辅助作用 生成节点类');
$node = new Node('root');
p($node,'生成一个根节点');
$node['left']='这是通过ArrayAccess修改的值加入的值';
p($node,'设置第一个左节点');
$node->right=['id'=>1,'name'=>'rightNode','other'=>[1,2,3,4]];
p($node,'设置第一个左节点');
