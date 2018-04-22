<?php
use wstree\Tree;
require '../vendor/autoload.php';
$tree=new Tree();

$tree
    ->tree('A')
        ->tree('B')
            ->tree('I')
                ->leaf('K')
                ->leaf('L')
                ->end()
            ->tree('J')
                ->leaf('M')
                ->leaf('N')
                ->closest()
        ->tree('C')
            ->tree('D')
                ->leaf('G')
                ->leaf('H')
                ->end()
            ->tree('E')
                ->leaf('F');
p($tree,'这是通过 tree()、leaf()、end()、closest()、方法生成一颗二叉树的例子');
