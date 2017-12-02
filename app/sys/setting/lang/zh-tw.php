<?php
/**
 * The zh-tw file of setting module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青島易軟天創網絡科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     setting 
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
$lang->setting->common = '設置';
$lang->setting->reset  = '恢復預設';
$lang->setting->key    = '鍵';
$lang->setting->value  = '值';

$lang->setting->lang = '產品狀態、產品綫、客戶類型、客戶規模、客戶等級、客戶狀態、貨幣設置、角色';

$lang->setting->customerPool = '客戶池設置';
$lang->setting->modules      = '功能模組設置';

$lang->setting->module = new stdClass();
$lang->setting->module->user     = '員工角色';
$lang->setting->module->product  = '產品狀態';
$lang->setting->module->customer = '客戶級別';

$lang->setting->user = new stdClass();
$lang->setting->user->fields['roleList'] = '角色設置';

$lang->setting->product = new stdClass();
$lang->setting->product->fields['statusList'] = '產品狀態';
$lang->setting->product->fields['lineList']   = '產品綫';

$lang->setting->product->lineList = new stdclass();
$lang->setting->product->lineList->key   = '代號';
$lang->setting->product->lineList->value = '名稱';

$lang->setting->customer = new stdClass();
$lang->setting->customer->fields['typeList']      = '客戶類型';
$lang->setting->customer->fields['sizeNameList']  = '客戶規模';
$lang->setting->customer->fields['levelNameList'] = '客戶等級';
$lang->setting->customer->fields['statusList']    = '客戶狀態';

$lang->setting->system = new stdclass();
$lang->setting->system->mainCurrency           = '主要貨幣';
$lang->setting->system->fields['currencyList'] = '貨幣設置';

$lang->setting->allLang     = '適用所有語言';
$lang->setting->currentLang = '適用當前語言';

$lang->setting->placeholder = new stdclass();
$lang->setting->placeholder->key   = '變數名';
$lang->setting->placeholder->value = '自定義顯示值';
$lang->setting->placeholder->info  = '詳細描述';

$lang->setting->placeholder->typeList = new stdclass();
$lang->setting->placeholder->typeList->key = '變數名，長度為1~30字元';

$lang->setting->placeholder->sizeNameList = new stdclass();
$lang->setting->placeholder->sizeNameList->key   = '數字和字母組合';
$lang->setting->placeholder->sizeNameList->value = '簡短描述';
$lang->setting->placeholder->sizeNameList->info  = '詳細描述';

$lang->setting->placeholder->levelNameList = new stdclass();
$lang->setting->placeholder->levelNameList->key   = '數字和字母組合';
$lang->setting->placeholder->levelNameList->value = '簡短描述';
$lang->setting->placeholder->levelNameList->info  = '詳細描述';

$lang->setting->placeholder->lineList = new stdclass();
$lang->setting->placeholder->lineList->key   = '數字和字母組合';
$lang->setting->placeholder->lineList->value = '簡短描述';

$lang->setting->reserveDays    = '進入客戶池（天）';
$lang->setting->reserveDaysTip = '在設定天數內沒有更新客戶信息（未簽約客戶），該客戶將自動進入客戶池。值設為0時禁用此功能。';
$lang->setting->currencyTip    = '以非主要貨幣記賬時需要錄入匯率。';

$lang->setting->moduleList['attend']   = '考勤';
$lang->setting->moduleList['leave']    = '請假';
$lang->setting->moduleList['makeup']   = '補班';
$lang->setting->moduleList['overtime'] = '加班';
$lang->setting->moduleList['lieu']     = '調休';
$lang->setting->moduleList['trip']     = '出差';
$lang->setting->moduleList['egress']   = '外出';
$lang->setting->moduleList['refund']   = '報銷';
