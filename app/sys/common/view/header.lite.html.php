<?php
/**
 * The header.lite view of common module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     common 
 * @version     $Id: header.lite.html.php 4041 2016-09-27 07:09:30Z liugang $
 * @link        http://www.ranzhico.com
 */
if($extView = $this->getExtViewFile(__FILE__)){include $extView; return helper::cd();}
$webRoot      = $config->webRoot;
$jsRoot       = $webRoot . "js/";
$themeRoot    = $webRoot . "theme/";
$clientTheme  = $this->app->getClientTheme();
?>
<!DOCTYPE html>
<html>
<head profile="http://www.w3.org/2005/10/profile">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <?php echo html::favicon($webRoot . 'favicon.ico');?>
  <?php
  if(!isset($title)) $title  = '';
  if(!empty($title)) $title .= $lang->minus;
  echo html::title($title . $lang->ranzhi);

  js::exportConfigVars();
  if(isset($this->app->entry->id)) js::set('entryID', $this->app->entry->id);
  if(RUN_MODE != 'upgrade' and RUN_MODE != 'install' and !isset($this->app->entry->id) and ($this->app->user->admin == 'super')) js::set('entryID', 'superadmin');
  if(RUN_MODE != 'upgrade' and RUN_MODE != 'install' and !isset($this->app->entry->id) and (strpos($config->dashboard->modules, $this->moduleName) !== false)) js::set('entryID', 'dashboard');
  if($config->debug)
  {
      js::import($jsRoot . 'jquery/min.js');
      js::import($jsRoot . 'zui/min.js');
      js::import($jsRoot . 'ranzhi.js');
      js::import($jsRoot . 'my.js');
      css::import($themeRoot . 'zui/css/min.css');
      css::import($themeRoot . 'default/style.css');
      css::import($themeRoot . 'default/admin.css');

      if($this->app->getModuleName() === 'index' && $this->app->getMethodName() === 'index') css::import($themeRoot . 'default/ips.css');

      if(strpos($clientTheme, 'default') === false) css::import($clientTheme . 'style.css', $config->version);
  }
  else
  {
      css::import($themeRoot . 'default/all.css');

      if($this->app->getModuleName() === 'index' && $this->app->getMethodName() === 'index') css::import($themeRoot . 'default/ips.css');

      if(strpos($clientTheme, 'default') === false) css::import($clientTheme . 'style.css', $config->version);

      js::import($jsRoot     . 'all.js');
  }

  if(RUN_MODE == 'admin') css::import($themeRoot . 'default/admin.css');
  if(isset($pageCSS)) css::internal($pageCSS);
?>
<!--[if lt IE 9]>
<?php
js::import($jsRoot . 'html5shiv/min.js');
js::import($jsRoot . 'respond/min.js');
?>
<![endif]-->
<!--[if lt IE 10]>
<?php
js::import($jsRoot . 'jquery/placeholder/min.js');
?>
<![endif]-->
<?php js::set('lang', $lang->js);?>
</head>
<body class='m-<?php echo $this->app->getModuleName() . '-' . $this->app->getMethodName() ?><?php if(isset($this->app->entry->code)) echo ' entry-' . $this->app->entry->code; ?>'>
