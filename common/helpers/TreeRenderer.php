<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Render Tree instead of Grid
 *
 */

namespace app\common\helpers;

use app\models\Notifications;
use Exception;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\web\JsExpression;
use yii\helpers\Html;

class TreeRenderer
{

    private static $_FORMDATA = null;
    private static $_SELECTED = null;
    private static $_TREEID = null;
    private static $_CHECKBOX = false;
    private static $_SINGLE = false;
    private static $_TREENAME = '';
    private static $_PARENT_FIELD = null;
    private static $_TITLE_FIELD = null;
    private static $_options = null;

    /**
     * @param array $options
     */
    public static function returnTree($options = [])
    {
        self::injectOptions($options);
        self::$_options = $options;
        try {
            ob_start();
            self::beginPjax(Common::incrementalHash());
            if (self::$_CHECKBOX) {
                self::returnTreeWithCheckbox(self::$_SINGLE);
            } else {
                self::returnTreeNormal();
            }
            self::endPjax();
            return ob_get_clean();
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    /**
     * @param bool $single
     */
    public static function returnTreeWithCheckbox($single = false)
    {
        $selected = Json::decode(self::$_SELECTED);
        $dataArray = Common::buildTree(self::$_FORMDATA, 0, $selected, self::$_PARENT_FIELD, self::$_TITLE_FIELD);

        self::checkboxTreeJs(self::$_TREEID, $selected);
        self::hiddenSelectedValues(self::$_TREEID);

        echo self::createTreeWidget(self::injectCheckBoxTreeOptions($dataArray, self::$_TREEID, $single));
    }

    /**
     * Shows tree widget
     */
    public static function returnTreeNormal()
    {
        echo self::createHiddenParentInput();
        echo self::createHiddenParentUrl();
        echo self::createTreeWidget(self::injectTreeViewOptions());
    }

    /**
     * @param $tree_id
     * @param $selected
     */
    public static function checkboxTreeJs($tree_id, $selected)
    {
        \Yii::$app->controller->view->registerJs(
            "
            var {$tree_id}_selected_nodes = JSON.parse('" . Json::encode($selected) . "');
            if({$tree_id}_selected_nodes.length==0){
                {$tree_id}_selected_nodes={};
            }
            var {$tree_id}_tree_object=null;
            ", \yii\web\View::POS_END
        );
        if (!self::$_SINGLE) {
            \Yii::$app->controller->view->registerJs(
                "
            $(document).ready(function(){
                var input = $('#{$tree_id}_input');
                input.parents('form').on('submit',function(){
                    input.val(JSON.stringify({$tree_id}_selected_nodes));
                })
            })
            ", \yii\web\View::POS_END
            );
        }
    }

    /**
     * @param $tree_id
     */
    public static function hiddenSelectedValues($tree_id)
    {
        $model = Common::checkIsset('model', self::$_options);
        $attribute = Common::checkIsset('attribute', self::$_options);
        $name = Common::checkIsset('name', self::$_options, 'selectedAttr');
        if ($model && $attribute) {
            self::$_SELECTED = json_encode([$model->$attribute]);
            echo Html::activeHiddenInput($model, $attribute, ['id' => $tree_id . '_input', 'value' => $model->$attribute]);
        } else {
            echo Html::hiddenInput($name, self::$_SELECTED, ['id' => $tree_id . '_input', 'value' => self::$_SELECTED]);
        }
    }

    /**
     * @param $dataArray
     * @param $tree_id
     * @param bool $single
     * @return array
     */
    public static function injectCheckBoxTreeOptions($dataArray, $tree_id, $single = false)
    {
        $treeViewArray = [
            'id' => $tree_id,
            'items' => $dataArray,
            'clientOptions' => [
                'selectMode' => 2,
                'folder' => 'false',
                'clickFolderMode' => 1,
                'checkbox' => true,
                'extensions' => [], //,'persist' 'dnd'
                'init' => new JsExpression('
                        function(event, data) {
                            ' . $tree_id . '_tree_object=data.tree;                        
                        }
                '),
                'select' => new JsExpression('function (event, data){

                    var node=data.node;
                    var id=node["key"];
                    
                    if (node.isSelected()) {
                       console.log(node.data.node_type);
                       ' . $tree_id . '_selected_nodes[id]=id
                        node.visit(function(childNode) {
                        childNode.setSelected(true);
                        });
                    }else{
                        delete ' . $tree_id . '_selected_nodes[id];
                        node.visit(function(childNode) {
                            childNode.setSelected(false);
                        });
                    }
                }')
            ],
        ];
        if ($single) {
            $treeViewArray['clientOptions']['selectMode'] = 1;
            $treeViewArray['clientOptions']['select'] = new JsExpression('function (event, data){
                    var node=data.node;
                    var id=node["key"];
                    console.log(node.isSelected());
                    if (node.isSelected()) {
                        if(node.data.node_type=="last_node"){
                            ' . $tree_id . '_selected_nodes[id]=id
                        }
                        $("#' . $tree_id . '_input").val(id);
                    }
                }');
        }
        return $treeViewArray;
    }

    /**
     * @param $options
     */
    private static function injectOptions($options)
    {
        self::$_FORMDATA = Common::checkIsset('dataArray', $options);
        self::$_SELECTED = Common::checkIsset('selectedAttr', $options, '[]');
        self::$_TREEID = Common::checkIsset('tree_id', $options, 'kadrer_tree');
        self::$_CHECKBOX = Common::checkIsset('checkbox', $options, false);
        self::$_SINGLE = Common::checkIsset('single', $options, false);
        self::$_PARENT_FIELD = Common::checkIsset('parent_field', $options, 'parent');
        self::$_TITLE_FIELD = Common::checkIsset('title_field', $options, 'title');
        self::$_TITLE_FIELD = Common::checkIsset('title_field', $options, 'title');
    }

    /**
     * @param string $id
     * @return \yii\base\Widget|Pjax
     */
    private static function beginPjax($id = 'yiitree')
    {
        return Pjax::begin(['id' => 'pcontainer_' . $id . self::$_TREENAME, 'timeout' => 10000]);
    }

    /**
     * @return \yii\base\Widget|Pjax
     */
    private static function endPjax()
    {
        return Pjax::end();
    }

    /**
     * @return array
     */
    private static function injectTreeViewOptions()
    {
        $id = \Yii::$app->request->get('id');

        $treeViewArray = [
            'id' => 'tree' . Common::incrementalHash() . self::$_TREENAME,
            'items' => self::getItems(),
            'clientOptions' => [
                'selectMode' => 1,
                'autoCollapse' => true,
                'clickFolderMode' => 3,
                'extensions' => ['dnd'], //,'persist' 'dnd' 
                'dnd' => [
                    'preventVoidMoves' => true,
                    'preventRecursiveMoves' => true,
                    'autoExpandMS' => 400,
                    'dragStart' => new JsExpression('function(node, data) {
                                return true;
                            }'),
                    'dragEnter' => new JsExpression('function(node, data) {
                                return true;
                            }'),
                    'dragDrop' => new JsExpression('function(node, data) {
                          console.log(data);
                          setTimeout(function(){
                            var _parent = null;
                                     if(data.otherNode.parent.parent !== null){
                                       _parent = data.otherNode.parent.key;// data.node.key  
                                     }else{
                                       _parent = "";  
                                     }
                                       $.ajax({
                                        url:$("#save_url").val()+"?id="+data.otherNode.data.id+"&for_tree=1&parent="+_parent,
                                        type:"POST",
                                        dataType:"Json",
                                        data:data.otherNode.data,
                                        success:function(result){
                                        
                                        }
                                   });
                          },0)
                                data.otherNode.moveTo(node, data.hitMode);
                            }'),
                ],
                'activate' => new JsExpression('
                        function(node, data) {
                              node  = data.node;
                              var parentName = $("#parent_name").val();
                              $(".op_buttons").find(".btn").each(function(){
                                  var $this = $(this),
                                  a_href=$this.attr("href"),
                                   regexp ="&"+parentName+"=[0-9]{1,}" ,
                                   RegObjParent = new RegExp(regexp,"g"),
                                   RegObjID = new RegExp("&id=[0-9]{1,}","g");
                                a_href = a_href.replace(RegObjParent,"");
                                a_href = a_href.replace(RegObjID,"");
                                if($this.hasClass("btn_create")){
                                 a_href = a_href +"&"+parentName+"="+node.key;
                               }
                               if($this.hasClass("btn_update") || $this.hasClass("btn_view") || $this.hasClass("btn_delete") || $this.hasClass("btn_show") ){
                                 a_href = a_href +"&id="+node.key;
                                  
                               }
                                $this.attr("data-id",node.key);
                                $this.attr("href",a_href);
                              })
                        }
                '),
                'init' => new JsExpression('
                        function(event, data) {
                             if($("#id_offices").length){
                             var office_id =  getParameterByName("id");
                                data.tree.activateKey(office_id);
                             }
                        }
                       
                '),
                'dblclick' => new JsExpression('
                        function(node, data) {
                        var _href = $(".op_buttons").find(".btn_show").attr("href");
                        
                        var _key = data.node.key;
                        if(typeof _href !== "undefined" && !$(this).parents(".offices-search-class").length){
                         window.location.href  = _href;
                        }else{
                          if(!data.node.hasChildren()){
                               $("body").find(".move_product").attr("data-id",_key).click();  
                            }
                         }
                      }
                       
                '),
            ]
        ];
        return $treeViewArray;
    }

    /**
     * @param $treeViewArray
     * @throws Exception
     */
    private static function createTreeWidget($treeViewArray)
    {
        echo \yii2mod\tree\Tree::widget($treeViewArray);
    }

    /**
     * @return array
     */
    private static function getItems()
    {
        $items = Common::buildTree(self::$_FORMDATA, 0, [], self::$_PARENT_FIELD, self::$_TITLE_FIELD);
        return $items;
    }

    /**
     * @return string
     */
    private static function createHiddenParentInput()
    {
        return Html::hiddenInput('parent_name', self::getParentForTree(), ['id' => 'parent_name']);
    }

    /**
     * @return string
     */
    private static function createHiddenParentUrl()
    {
        $save_url = Common::checkIsset('save_url', self::$_options, Url::to(['move']));
        return Html::hiddenInput('parent_url', \Yii::$app->urlManager->createUrl(["admin/item/getparentfieldname"]), ['id' => 'parent_url']) . ' '
            . Html::hiddenInput('save_url', $save_url, ['id' => 'save_url']);
    }

    /**
     * @param string $output
     * @return string
     */
    private static function getParentForTree($output = "string")
    {
        return 'parent';
    }

}
