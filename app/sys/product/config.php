<?php
/**
 * The config file of product module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     product 
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
$config->product->require = new stdclass();
$config->product->require->create = 'name, code';
$config->product->require->edit   = 'name, code';

global $lang, $app;
$config->product->search['module'] = 'product';

$config->product->search['fields']['id']          = $lang->product->id;
$config->product->search['fields']['name']        = $lang->product->name;
$config->product->search['fields']['code']        = $lang->product->code;
$config->product->search['fields']['type']        = $lang->product->type;
$config->product->search['fields']['status']      = $lang->product->status;
$config->product->search['fields']['line']        = $lang->product->line;
$config->product->search['fields']['desc']        = $lang->product->desc;
$config->product->search['fields']['createdBy']   = $lang->product->createdBy;
$config->product->search['fields']['createdDate'] = $lang->product->createdDate;
$config->product->search['fields']['editedBy']    = $lang->product->editedBy;
$config->product->search['fields']['editedDate']  = $lang->product->editedDate;

$config->product->search['params']['id']            = array('operator' => '=',       'control' => 'input',  'values' => '');
$config->product->search['params']['name']          = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->product->search['params']['code']          = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->product->search['params']['type']          = array('operator' => '=',       'control' => 'select', 'values' => array('' => '') + $lang->product->typeList);
$config->product->search['params']['status']        = array('operator' => '=',       'control' => 'select', 'values' => array('' => '') + $lang->product->statusList);
$config->product->search['params']['line']          = array('operator' => '=',       'control' => 'select', 'values' => $lang->product->lineList);
$config->product->search['params']['desc']          = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->product->search['params']['createdBy']     = array('operator' => '=',       'control' => 'select', 'values' => 'users');
$config->product->search['params']['createdDate']   = array('operator' => '>=',      'control' => 'input',  'values' => '', 'class' => 'date');
$config->product->search['params']['editedBy']      = array('operator' => '=',       'control' => 'select', 'values' => 'users');
$config->product->search['params']['editedDate']    = array('operator' => '>=',      'control' => 'input',  'values' => '', 'class' => 'date');
