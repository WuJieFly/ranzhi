<?php
/**
 * The zh-cn file of block module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     block 
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
$lang->block->common    = '区块';
$lang->block->depositor = '付款账号';
$lang->block->lblBlock  = '区块';
$lang->block->admin     = '管理区块';
$lang->block->num       = '数量';
$lang->block->orderBy   = '排序';

$lang->block->availableBlocks = new stdclass();
$lang->block->availableBlocks->depositor = '付款账号';
$lang->block->availableBlocks->trade     = '账目';
$lang->block->availableBlocks->baseFacts = '收支概况';
$lang->block->availableBlocks->provider  = '供应商';
$lang->block->availableBlocks->report    = '报表';

$this->lang->block->orderByList->trade['id_asc']  = 'ID 递增';
$this->lang->block->orderByList->trade['id_desc'] = 'ID 递减';

$this->lang->block->typeList->trade['all']  = '全部';
$this->lang->block->typeList->trade['in']   = '收入';
$this->lang->block->typeList->trade['out']  = '支出';

$this->lang->block->groupByList = new stdclass();
$this->lang->block->groupByList->trade['category'] = '按科目统计';
$this->lang->block->groupByList->trade['dept']     = '按部门统计';

$this->lang->block->orderByList->provider['id_asc']  = 'ID 递增';
$this->lang->block->orderByList->provider['id_desc'] = 'ID 递减';
