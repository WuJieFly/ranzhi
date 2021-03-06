<?php
/**
 * The upgrade module zh-tw file of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青島易軟天創網絡科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     upgrade
 * @version     $$
 * @link        http://www.ranzhico.com
 */
$lang->upgrade = new stdclass();
$lang->upgrade->common  = '升級';

$lang->upgrade->result  = '升級結果';
$lang->upgrade->fail    = '升級失敗';
$lang->upgrade->success = '升級成功';
$lang->upgrade->tohome  = '返迴首頁';

$lang->upgrade->index         = '檢查是否可以執行升級程序';
$lang->upgrade->backup        = '備份數據';
$lang->upgrade->selectVersion = '確認升級之前的版本';
$lang->upgrade->confirm       = '確認要執行的SQL語句';
$lang->upgrade->execute       = '確認執行';
$lang->upgrade->next          = '下一步';
$lang->upgrade->redeploy      = '請重新部署app檔案夾後繼續';
$lang->upgrade->redeployDesc  = "<h5>因為代碼結構調整,需要重新部署app目錄。</h5><div class='text-important'>操作方法:刪除舊的app目錄，再從新的安裝包裡面複製app檔案夾。</div>";
$lang->upgrade->removeTodo    = '請刪除 %s 檔案夾後繼續';
$lang->upgrade->removeTodoTip = "<h5>因為代碼結構調整,需要刪除%s目錄。</h5><div class='text-important'>操作方法:刪除舊的%s檔案夾。</div>";
$lang->upgrade->updateLicense = '然之協同 2. 0 已更換授權協議至 Z PUBLIC LICENSE(ZPL) 1.1。';

$lang->upgrade->majorList['3_5'] = array();
$lang->upgrade->majorList['3_5']['1'] = '主營業務收入';
$lang->upgrade->majorList['3_5']['2'] = '非主營業務收入';
$lang->upgrade->majorList['3_5']['3'] = '主營業務成本';
$lang->upgrade->majorList['3_5']['4'] = '非主營業務成本';

$lang->upgrade->majorList['3_6'] = array();
$lang->upgrade->majorList['3_6']['5'] = '理財盈利';
$lang->upgrade->majorList['3_6']['6'] = '理財虧損';
$lang->upgrade->majorList['3_6']['7'] = '手續費';
$lang->upgrade->majorList['3_6']['8'] = '借貸利息';

$lang->upgrade->backupData = <<<EOT
<pre>
<strong>使用phpMyAdmin或者mysqldump命令備份資料庫。</strong>
<code class='red'>$ mysqldump -u %s</span> -p%s %s > ranzhi.sql</code>
</pre>
EOT;

$lang->upgrade->versionNote = "務必選擇正確的版本，否則會造成數據丟失。";

$lang->upgrade->fromVersions['1_0_beta'] = '1.0.beta';
$lang->upgrade->fromVersions['1_1_beta'] = '1.1.beta';
$lang->upgrade->fromVersions['1_2_beta'] = '1.2.beta';
$lang->upgrade->fromVersions['1_3_beta'] = '1.3.beta';
$lang->upgrade->fromVersions['1_4_beta'] = '1.4.beta';
$lang->upgrade->fromVersions['1_5_beta'] = '1.5.beta';
$lang->upgrade->fromVersions['1_6']      = '1.6';
$lang->upgrade->fromVersions['1_7']      = '1.7';
$lang->upgrade->fromVersions['2_0']      = '2.0';
$lang->upgrade->fromVersions['2_1']      = '2.1';
$lang->upgrade->fromVersions['2_2']      = '2.2';
$lang->upgrade->fromVersions['2_3']      = '2.3';
$lang->upgrade->fromVersions['2_4']      = '2.4';
$lang->upgrade->fromVersions['2_5']      = '2.5';
$lang->upgrade->fromVersions['2_6']      = '2.6';
$lang->upgrade->fromVersions['2_7']      = '2.7';
$lang->upgrade->fromVersions['3_0']      = '3.0';
$lang->upgrade->fromVersions['3_1']      = '3.1';
$lang->upgrade->fromVersions['3_2']      = '3.2';
$lang->upgrade->fromVersions['3_2_1']    = '3.2.1';
$lang->upgrade->fromVersions['3_3']      = '3.3';
$lang->upgrade->fromVersions['3_4']      = '3.4';
$lang->upgrade->fromVersions['3_5']      = '3.5';
$lang->upgrade->fromVersions['3_6']      = '3.6';
$lang->upgrade->fromVersions['3_7']      = '3.7';
$lang->upgrade->fromVersions['4_0']      = '4.0';
$lang->upgrade->fromVersions['4_1']      = '4.1';
$lang->upgrade->fromVersions['4_2']      = '4.2';
$lang->upgrade->fromVersions['4_2_1']    = '4.2.1';
$lang->upgrade->fromVersions['4_2_2']    = '4.2.2';
$lang->upgrade->fromVersions['4_2_3']    = '4.2.3';
$lang->upgrade->fromVersions['4_3_beta'] = '4.3.beta';
$lang->upgrade->fromVersions['4_4']      = '4.4';
$lang->upgrade->fromVersions['4_5']      = '4.5';

$lang->upgrade->deleteTips   = '需要刪除部分檔案。linux下面命令為：<br />';
$lang->upgrade->deleteDir    = '<code>rm -fr %s</code>';
$lang->upgrade->deleteFile   = '<code>rm %s</code>';
$lang->upgrade->afterDeleted = '<br />刪除以上檔案後刷新！';
