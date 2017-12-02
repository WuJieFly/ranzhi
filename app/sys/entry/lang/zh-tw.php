<?php
/**
 * The zh-tw file of entry module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青島易軟天創網絡科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     entry 
 * @version     $Id: zh-tw.php 4091 2016-09-30 07:16:50Z daitingting $
 * @link        http://www.ranzhico.com
 */
$lang->entry->common      = '應用';
$lang->entry->admin       = '應用列表';
$lang->entry->create      = '添加應用';
$lang->entry->edit        = '編輯應用';
$lang->entry->delete      = '刪除應用';
$lang->entry->createKey   = '重新生成密鑰';
$lang->entry->order       = '排序';
$lang->entry->style       = '樣式';
$lang->entry->setCategory = '維護分組';

$lang->entry->name        = '名稱';
$lang->entry->abbr        = '縮寫';
$lang->entry->code        = '代號';
$lang->entry->buildin     = '內置應用';
$lang->entry->integration = '整合';
$lang->entry->key         = '密鑰';
$lang->entry->block       = '區塊地址';
$lang->entry->ip          = 'IP列表';
$lang->entry->logo        = 'Logo';
$lang->entry->login       = '訪問網址';
$lang->entry->logout      = '退出地址';
$lang->entry->nothing     = '暫時沒有應用';
$lang->entry->open        = '打開方式';
$lang->entry->control     = '窗口控制條';
$lang->entry->size        = '窗口大小';
$lang->entry->position    = '顯示位置';
$lang->entry->width       = '寬';
$lang->entry->height      = '高';
$lang->entry->priv        = '權限';
$lang->entry->category    = '分組';

$lang->entry->chanzhi          = '蟬知';
$lang->entry->zentao           = '禪道';
$lang->entry->integrateChanzhi = '整合蟬知';
$lang->entry->integrateZentao  = '整合禪道';

$lang->entry->chanzhiPlaceholder = '請輸入蟬知的後台訪問地址';
$lang->entry->chanzhiURL         = '後台入口';
$lang->entry->zentaoPlaceholder  = '如：http://www.zentaopms.com/user-login.html';
$lang->entry->zentaoURL          = '禪道登錄地址';

$lang->entry->zentaoAdmin   = '禪道管理員';
$lang->entry->adminAccount  = '管理員賬號';
$lang->entry->adminPassword = '管理員密碼';
$lang->entry->bindUser      = '綁定用戶';
$lang->entry->nextStep      = '下一步';
$lang->entry->createUser    = '新建';

$lang->entry->confirmDelete = '您確定刪除該應用嗎？';
$lang->entry->lblBlock      = '區塊';
$lang->entry->editWarnning  = '系統內置應用，請謹慎修改。';

$lang->entry->note = new stdClass();
$lang->entry->note->name    = '授權應用名稱';
$lang->entry->note->abbr    = '兩個字元縮寫';
$lang->entry->note->logo    = 'Logo尺寸：64*64，如果上傳png格式，務必保持圖片透明';
$lang->entry->note->code    = '授權應用代號，必須為英文、數字或下劃線的組合';
$lang->entry->note->login   = '訪問應用的地址或登錄應用的表單的提交地址';
$lang->entry->note->logout  = '退出應用的地址';
$lang->entry->note->visible = '左側顯示';
$lang->entry->note->api     = '應用獲取區塊的介面地址';
$lang->entry->note->ip      = "允許訪問應用的ip，多個ip用逗號隔開。支持IP段，如192.168.1.*";
$lang->entry->note->allip   = '無限制';
$lang->entry->note->scheme  = '當前訪問協議為https，內嵌窗口只能打開https協議的應用網址。';

$lang->entry->error = new stdClass();
$lang->entry->error->name  = '名稱不能為空';
$lang->entry->error->code  = '代號不能為空';
$lang->entry->error->key   = '密鑰不能為空';
$lang->entry->error->ip    = 'IP列表不能為空';
$lang->entry->error->url   = ' 非內置應用的登錄地址，必須包含 /、http://或者https://';

$lang->entry->error->admin         = '管理員用戶名或密碼錯誤';
$lang->entry->error->zentaoSetting = '禪道系統設置失敗';
$lang->entry->error->version       = '您的禪道系統版本低於%s';
$lang->entry->error->zentaoUrl     = '禪道登錄地址錯誤';
$lang->entry->error->accessDenied  = '訪問受限';

$lang->entry->openList['blank']  = '新開標籤';
$lang->entry->openList['iframe'] = '內嵌窗口';

$lang->entry->sizeList['max']    = '最大化';
$lang->entry->sizeList['custom'] = '自定義';

$lang->entry->positionList['default'] = '預設';
$lang->entry->positionList['center']  = '居中';

$lang->entry->controlList['none']   = '無';
$lang->entry->controlList['full']   = '完整';
$lang->entry->controlList['simple'] = '透明';

$lang->entry->integrationList[1] = '啟用';
$lang->entry->integrationList[0] = '關閉';
