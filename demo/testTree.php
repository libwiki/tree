<?php
use tree\Tree;
require '../vendor/autoload.php';
$tree=new Tree();

$tree
    ->tree('A')
        ->leaf('B')
        ->tree('C')
            ->tree('D')
                ->leaf('G')
                ->leaf('H')
                ->end()
            ->tree('E')
                ->leaf('F')
                ->end();
p($tree,'这是通过 tree()、leaf()、end()、方法生成一颗二叉树的例子');
