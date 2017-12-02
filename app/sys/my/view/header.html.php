<?php
/**
 * The header view of my module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     my 
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include $app->getModuleRoot() . '../sys/common/view/header.lite.html.php';?>
<style>body {padding-top: 58px}</style>
<nav class='navbar navbar-main navbar-fixed-top' id='mainNavbar'>
  <?php if($this->app->appName == 'sys'):?>
  <div class='collapse navbar-collapse'>
    <ul class='nav navbar-nav'>
      <li><?php echo html::a($this->createLink('user', 'profile'), "<i class='icon-user'></i> " . $app->user->realname, "data-toggle='modal' data-id='profile'");?></li>
    </ul>
    <?php echo commonModel::createDashboardMenu();?>
  </div>
  <?php else:?>
  <div class='navbar-header'>
    <?php echo html::a($this->createLink($this->config->default->module), $lang->app->name, "class='navbar-brand'");?>
  </div>
  <?php echo commonModel::createMainMenu($this->moduleName);?>
  <?php endif;?>
</nav>
<?php 
if(!isset($moduleMenu)) $moduleMenu = $this->my->createModuleMenu($this->methodName);
if($moduleMenu) echo "$moduleMenu\n<div class='row page-content with-menu'>\n"; else echo "<div class='row page-content'>";
?>
