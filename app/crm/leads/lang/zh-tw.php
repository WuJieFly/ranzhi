<?php
if(!isset($lang->leads)) $lang->leads = new stdclass();

$lang->leads->common    = '名單';
$lang->leads->browse    = '瀏覽名單';
$lang->leads->create    = '添加名單';
$lang->leads->edit      = '編輯名單';
$lang->leads->delete    = '刪除名單';
$lang->leads->view      = '名單詳情';
$lang->leads->apply     = '申請';
$lang->leads->assign    = '指派';
$lang->leads->transform = '確認';
$lang->leads->ignore    = '忽略';
$lang->leads->settings  = '設置';
$lang->leads->applyRule = '派發規則';

$lang->leads->list = '名單列表';

$lang->leads->applyLimit   = '每次申請記錄數';
$lang->leads->applyRemain  = '最多未處理記錄數';
$lang->leads->ignoreReason = '原因';

$lang->leads->tips = new stdclass();
$lang->leads->tips->apply       = '請先處理現有的名單聯繫人。';
$lang->leads->tips->applyRemain = '未處理的名單數低於此值才可以再次申請';
