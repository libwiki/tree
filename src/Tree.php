<?php
namespace wstree;
class Tree implements \ArrayAccess{
    public $root=null;
    public $current=null;


    /**
     * 初始化一个二叉树
     * @param  array   $array 用户数据
     * @param  array   $options 额外参数
     * @param  Node    $parent 起始节点
     * @param  integer $pid  起始pid
     * @param  boolean  $sort   是否先进行按主键值的排序
     * @param  integer  $level  级别默认为0 不建议传递
     * @param  string   $pidKey  父级ID 键名
     * @return array
     * $options=[$pid=0,$sort=true,$level=0,$pidKey='pid'];
     */
    function init($array,$options=[],$parent=null,$isFirst=true){
        if($isFirst){
            $pidKey=isset($options['pidKey'])?$options['pidKey']:'pid';
            $options=array_merge([$pidKey=>0,'sort'=>true,'level'=>0,'pidKey'=>'pid'],$options);
            $array=$this->childrens($array);
        }
        $options['level']++;
        extract($options);
        if($sort){
            // 排序 待进行
            usort($array,function($a,$b){
                return $a['id']>$b['id']?1:-1;
            });
            $options['sort']=false;
        }
    	foreach($array as $v){
    		if($v[$pidKey]==$pid){
                $v['_level']=$level;
    			$node=new Node($v);
                if(is_null($parent)){
                    $this->root=$node;
                }elseif(is_null($parent['left'])){
                    $node['parent']=$parent;
                    $parent['left']=$node;
                }else{
                    $node['parent']=$parent;
                    $parent['right']=$node;
                }
                $options[$pidKey]=$v['id'];
    			$this->init($array,$options,$node,false);
    		}
    	}
    }
    /**
     * 插入新节点(公排|弱区)
     * @param  mixed $key  插入的值
     * @param  Node $node  节点的实例
     * @param  boolean $natural 默认：false 是否自然排序(公排 从上到下 左到右) 默认为平衡排列(优选弱区)
     * @return Node
     */
    function insert($key,$node=null,$natural=false){
        $newNode=new Node($key);
        if(is_null($node)){
            $node=$this->root;
        }
        $rs=$this->insertable($node,$natural);
        if(!$rs){
            $this->root=$newNode;
        }else{
            if(is_null($rs->left)){
                $rs->left=$newNode;
            }else{
                $rs->right=$newNode;
            }
            $newNode->parent=$rs;
        }
        return $newNode;
    }
    /**
     * 查询可插入的节点信息(公排|弱区)
     * 通常作为插入用户时获取新用户的父节点
     * @param  Node $node 节点的实例
     * @param  boolean $natural 默认：false 是否自然排序(公排 从上到下 左到右) 默认为平衡排列(优选弱区)
     * @return array      [node,position]
     */
    function insertable($node,$natural=false){
        if(is_null($node)&&is_null($this->root)){
            return;
        }
        if(is_null($node['left'])||is_null($node['right'])){
            return $node;
        }
        if($natural){
            return $this->naturalPos($node);
        }
        $l_height=$this->getHeight($node['left']);
        $r_height=$this->getHeight($node['right']);

        if($r_height<$l_height){
            return $this->getPos($node['right'],2);
        }elseif($r_height==$l_height){
            $is_full=$this->isFull($node);
            if($is_full){ //满二叉树 最左边
                return $this->getPos($node['left'],1);
            }
            $isComplete=$this->isComplete($node);
            if($isComplete){ //完全二叉树 ROOT右边
                return $this->getPos($node['right'],2);
            }
            //非完全二叉树 ROOT左边
            return $this->getPos($node['left'],2);
        }else{
            return $this->getPos($node['left'],2);
        }

    }
    /**
     * 获取公排接点
     * @param  Node $node   节点的实例
     * @return Node 新节点插入的父节点
     */
    private function naturalPos($node){
        if(is_null($node['left'])||is_null($node['right'])){
            return $node;
        }
        $l_height=$this->getMinHeight($node['left']);
        $r_height=$this->getMinHeight($node['right']);
        if($r_height<$l_height){
            return $this->naturalPos($node['right']);
        }
        return $this->naturalPos($node['left']);

    }
    /**
     * 获取弱区接点
     * @param  Node $node   节点的实例
     * @param  integer $type 参考: $this->insertable();
     * @return Node 新节点插入的父节点
     */
    private function getPos($node,$type=1){
        if($type==1){ //最左边
            if(is_null($node['left'])){
                return $node;
            }else{
                return $this->getPos($node['left'],$type);
            }
        }else{
            $is_full=$this->isFull($node['left']);
            if(is_null($node['right'])||is_null($node['left'])){
                return $node;
            }
            if($is_full){ //右边
                if(is_null($node['left']['left'])){
                    return $node['left'];
                }elseif(is_null($node['right']['left'])){
                    return $node['right'];
                }else{
                    $is_full=$this->isFull($node);
                    if($is_full){
                        return $this->getPos($node['left'],$type);
                    }else{
                        return $this->getPos($node['right'],$type);
                    }
                }
            }else{ //最左边父级
                if(is_null($node['left'])){
                    return $node['parent'];
                }else{
                    return $this->getPos($node['left'],$type);
                }
            }

        }
    }
    /**
     * 是否满二叉树
     * @param  Node  $node 当前节点
     * @param  boolean  $getNode 当前节点
     * @return boolean
     */
    function isFull($node=null){
        if(is_null($node)){
            $node=$this->root;
        }
        $length=0;
        $this->preOrder($node,function($item)use(&$length){
            $length++;
        });
        $height=$this->getHeight($node);
        return $length===pow(2,$height)-1;
    }
    /**
     * 是否完全二叉树
     * @param  Node  $node 当前节点
     * @param  boolean  $getNode 当前节点
     * @param  function $callback 一个回调函数 注入参数 在insertable()中使用
     * @return boolean
     */
    function isComplete($node=null,$callback=null){
        $breakPoint=false;
        $isComplete=true;
        $breakNode=null;
        $this->levelOrder($node,function($item)use(&$breakPoint,&$isComplete,&$breakNode){
            if(is_null($item)&&!$breakPoint){
                $breakPoint=true;
            }
            if(!$breakPoint){
                $parent=$item->parent;
                if(!isset($parent['right'])||is_null($parent['right'])){
                    $breakNode=$parent;
                }else{
                    $parent=$parent->parent;
                    if(is_null($parent)){
                        $breakNode=$item;
                    }elseif(!is_null($parent->right)){
                        $breakNode=$parent->right;
                    }
                }
            }
            if($breakPoint&&!is_null($item)){
                $isComplete=false;
            }
        });
        if(is_callable($callback)){
            call_user_func($callback,$breakNode);
        }
        //echo "<br>breakNode:".$breakNode->value;
        return $isComplete;
    }
    /**
     * 获取当前节点深度
     * @param  Node $node 节点的实例
     * @return integer
     */
    function getDepth($node){
        if(is_null($node)){
            return 0;
        }
        return $this->getDepth($node['parent'])+1;
    }
    /**
     * 获取当前节点高度
     * @param  Node $node 节点的实例
     * @return integer
     */
    function getHeight($node){
        if(is_null($node)){
            return 0;
        }
        return max($this->getHeight($node['left']),$this->getHeight($node['right']))+1;

    }
    /**
     * 获取当前节点高度(最短的那一边)
     * @param  Node $node 节点的实例
     * @return integer
     */
    function getMinHeight($node){
        if(is_null($node)){
            return 0;
        }
        return min($this->getMinHeight($node['left']),$this->getMinHeight($node['right']))+1;
    }
    /**
     * 设置一个节点
     * @param  mixed $key  需要设置的值
     * @return self 返回current指向$key生成的节点（当前节点）的当前类
     */
    function tree($key){
        $node=new Node($key);
        if(!is_null($this->root)&&!is_null($this->current)){
            $this->_addNode($node);
            $this->current=$node;
            return $this;
        }
        if(is_null($this->root)){
            $this->root=$node;
        }
        if(is_null($this->current)){
            $this->current=$node;
        }
        return $this;
    }
    /**
     * 设置一个叶节点(如果这是第一个则与tree()作用相同)
     * @param  mixed $key  需要设置的值
     * @return self 返回current指向当前节点父级的当前类
     */
    function leaf($key){
        if(is_null($this->root)||is_null($this->current)){
            return $this->tree($key);
        }
        $node=new Node($key);
        $this->_addNode($node);
        return $this;
    }
    /**
     * 返回指向上级节点(根节点返回的依然是根节点)的当前类
     * @return self 返回current指向父级的父级类
     */
    function end(){
        $current=$this->current;
        if(!is_null($current->parent)){
            $this->current=$current->parent;
        }
        return $this;
    }
    /**
     * 将指针指向最近的可插入节点的节点（如果均不满足则返回$this->root）
     * @param  integer $childNums 子集数要求
     * @return self 返回current指向$key生成的节点（当前节点）的当前类
     */
    function closest($childNums=2){
        $current=$this->current;
        if(!is_null($current['parent'])){
            $parent=$current['parent'];
            $this->current=$parent;
            $childs=0;
            if(!is_null($parent['left'])){
                $childs++;
            }
            if(!is_null($parent['right'])){
                $childs++;
            }
            if($childNums<=0||$childs<$childNums){
                return $this;
            }else{
                return $this->end();
            }
        }

    }
    /**
     * 增加一个节点(tree()、leaf()的助手方法)
     * @param Node $node 节点的实例
     */
    private function _addNode($node){
        $current=$this->current;
        $node['parent']=$this->current;
        if(is_null($current['left'])){
            $this->current->left=$node;
        }else{
            $this->current->right=$node;
        }
    }


    /**
     * 上下左右遍历(层层遍历)
     * @param  callable $callback 一个回调函数 接收一个Node实例参数
     * （这里的参数与另三个遍历参数有区别 主要为了应用于$this->isComplete()方法中）
     * @param  Node $node       初始节点的实例
     * @return void
     */
    function levelOrder($node,$callback){
        $queue=[$node];
        call_user_func($callback,$node);
        $parent=array_shift($queue);
        while(!is_null($parent)) {
            $left=$parent->left;
            $right=$parent->right;
            call_user_func($callback,$left);
            call_user_func($callback,$right);
            if(!is_null($left)&&!is_null($right)){
                array_push($queue,$left,$right);
            }elseif(!is_null($right)){
                array_push($queue,$left);
            }
            $parent=array_shift($queue);
        }
    }
    /**
     * 先序遍历
     * @param  Node $node       初始节点的实例
     * @param  callable $callback 一个回调函数 接收一个Node实例参数
     * @return void
     */
    function preOrder($node,$callback){
        if(!is_null($node)){
            call_user_func($callback,$node);
            $this->preOrder($node['left'],$callback);
            $this->preOrder($node['right'],$callback);
        }
    }
    /**
     * 中序遍历
     * @param  Node $node       初始节点的实例
     * @param  callable $callback 一个回调函数 接收一个Node实例参数
     * @return void
     */
    function inOrder($node,$callback){
        if(!is_null($node)){
            $this->inOrder($node['left'],$callback);
            call_user_func($callback,$node);
            $this->inOrder($node['right'],$callback);
        }
    }
    /**
     * 后序遍历
     * @param  Node $node       初始节点的实例
     * @param  callable $callback 一个回调函数 接收一个Node实例参数
     * @return void
     */
    function postOrder($node,$callback){
        if(!is_null($node)){
            $this->postOrder($node['left'],$callback);
            $this->postOrder($node['right'],$callback);
            call_user_func($callback,$node);
        }
    }
    /**
     * 先序 排序 以及是否存在子集判断
     * @param  array   $array 用户数据
     * @param  array   $data 引用类型最终返回的数据
     * @param  integer $pid  起始pid
     * @param  boolean  $sort   是否先进行按主键值的排序
     * @param  integer  $level  级别默认为0 不建议传递
     * @param  string   $pidKey  父级ID 键名
     * @return array
     * $options=[$pid=0,$sort=true,$level=0,$pidKey='pid'];
     */
    static function preSort($array,&$data,$options=[],$isFirst=true){
        if($isFirst){
            $pidKey=isset($options['pidKey'])?$options['pidKey']:'pid';
            $options=array_merge([$pidKey=>0,'sort'=>true,'level'=>0,'pidKey'=>'pid'],$options);
        }
        extract($options);
        if($sort){
            // 排序 待进行
            usort($array,function($a,$b){
                return $a['id']>$b['id']?1:-1;
            });
            $options['sort']=false;
        }
        $nbsp='&nbsp;';
    	foreach($array as $v){
    		if($v[$pidKey]==$pid){
                $v['_level']=$level;
    			$v['_prefix']=str_pad('',$level*strlen($nbsp)*8,$nbsp);
    			$data[]=$v;
                $options[$pidKey]=$v['id'];
                $options['level']++;
    			self::preSort($array,$data,$options,false);
    		}
    	}
    }

    /**
     * 子集个数统计（直属下级）
     * @param  array   $arr     统计数据
     * @param  string  $pidKey  父级ID 键名
     * @return array
     */
    static function childrens($arr,$pidKey='pid'){
        foreach ($arr as $k => $v) {
            $_childrens=0;
            $id=$v['id'];
            $_childrens=array_filter($arr,function($item)use($id,$pidKey){
                return $item[$pidKey]===$id?true:false;
            });
            // 拥有子节点数量(直接下级数)
            $v['_childrens']=count($_childrens);
            $arr[$k]=$v;
        }
        return $arr;
    }

    // 私有化扩展 待定
    function setValue($key,$value){
        $this->$key=$value;
    }
    function offsetExists($key){
        return isset($this->$key);
    }
    function offsetGet($key){
        return $this->$key;
    }
    function offsetSet($key, $value){
        $this->$key=$value;
    }
    function offsetUnset($key){
        //unset($this->$key);
    }
}
