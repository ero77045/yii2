<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Common
 *
 * @author Rafayel Khachatryan
 */

namespace app\common\helpers;

class Common
{

    private static $COUNTER = 1;

    public static function createDateTime($string, $format = null)
    {
        if (null === $format) {
            $format = \DateTime::ATOM;
        }

        return \DateTime::createFromFormat('d-m-Y H:i:s', $string)->format($format);
    }

    public static function passwordHash($string)
    {
        return hash('sha256', $string . self::getPasswordSalt());
    }

    public static function incrementalHash($length = 8)
    {
        $string = '';
        $characters = "23456789ABCDEFHJKLMNPRTVWXYZabcdefghijklmnopqrstuvwxyz";

        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        return $string;
    }

    private static function getPasswordSalt()
    {
        return "QxLUF1bgIAdeQX";
    }

    public static function checkIsset($variable, $array, $default = null)
    {
        if (isset($array[$variable]))
            return $array[$variable];
        return $default;
    }

    public static function likeMerge($arr = [], $arr1 = [])
    {
        $merge = [];
        if (!empty($arr)) {
            foreach ($arr as $k => $v) {
                $merge[$k] = $v;
            }
        }
        if (!empty($arr1)) {
            foreach ($arr1 as $k1 => $v1) {
                if (array_key_exists($k1, $merge) && is_array($merge[$k1])) {
                    $merge[$k1]['name'] = array_merge($merge[$k1]['name'], $v1['name']);
                } else {
                    $merge[$k1] = $v1;
                }
            }
        }
        return $merge;
    }

    public static function buildTree(array $elements, $parentId = 0, $selected_nodes = [], $parent_for_tree = 'parent', $main_field = 'title')
    {
        $branch = array();

        foreach ($elements as $index => $element) {

            $element['key'] = $element['id'];
            if (!empty($selected_nodes) && in_array($element['id'], $selected_nodes)) {
                $element['selected'] = true;
            }
            $element['title'] = array_key_exists($main_field, $element) ? $element[$main_field] : '';
            if (array_key_exists($parent_for_tree, $element) && $element[$parent_for_tree] == $parentId) {
                unset($elements[$index]);
                $children = self::buildTree($elements, $element['id'], $selected_nodes, $parent_for_tree, $main_field);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    public static function buildTreeWithLastNodes(array $elements, $selected_nodes = [], $parentId = 0, $main_field = '', $parent_for_tree = 'parent', $deep = null, &$last_nodes = [], $main_loop = true)
    {
        $branch = array();
        if ($deep !== null && self::$COUNTER >= $deep)
            return;

        foreach ($elements as $index => &$element) {
            $element['key'] = $element['id'];

            if (isset($element['node_type']) && $element['node_type'] == 'last_node') {
                if (in_array($element['id'], $selected_nodes)) {
                    $element['selected'] = true;
                }
                if (!isset($last_nodes[$element[$parent_for_tree]])) {
                    $last_nodes[$element[$parent_for_tree]] = [$element];
                } else {
                    $last_nodes[$element[$parent_for_tree]][] = $element;
                }
                unset($elements[$index]);
                continue;
            }

            if (array_key_exists($parent_for_tree, $element) && $element[$parent_for_tree] == $parentId) {
                $childrens = self::buildTreeWithLastNodes($elements, $selected_nodes, $element['id'], $main_field, $parent_for_tree, $deep, $last_nodes, false);

                if ($childrens) {
                    if (isset($last_nodes[$element['id']])) {
                        $childrens = array_merge($last_nodes[$element['id']], $childrens);
                        unset($last_nodes[$element['id']]);
                    }
                    $element['children'] = $childrens;
                } else {
                    if (isset($last_nodes[$element['id']])) {
                        $childrens = $last_nodes[$element['id']];
                        unset($last_nodes[$element['id']]);
                    }
                    $element['children'] = $childrens;
                }
                $branch[] = $element;
            }
            self::$COUNTER++;
        }
        if ($main_loop && count($last_nodes)) {
            foreach ($last_nodes as $users_group) {
                foreach ($users_group as $user) {
                    $branch[] = $user;
                }
            }
            //Natk::prettyPr($last_nodes);
        }
        return $branch;
    }

    public static function returnCommonParametrsForView()
    {
        return [
            'languages' => \app\models\Languages::find()->asArray()->all()
        ];
    }

}
