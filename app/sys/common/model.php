<?php
/**
 * The model file of common module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     common
 * @version     $Id: model.php 4150 2016-10-17 08:06:43Z liugang $
 * @link        http://www.ranzhico.com
 */
class commonModel extends model
{
    /**
     * Do some init functions.
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->startSession();
        $this->setUser();
        $this->setEntry();
        $this->loadConfigFromDB();
        if(!$this->checkIP()) die($this->lang->ipLimited);
        $this->loadLangFromDB();
    }

    /**
     * Load configs from database and save it to config->system and config->personal.
     * 
     * @access public
     * @return void
     */
    public function loadConfigFromDB()
    {
        /* Get configs of system and current user. */
        $account = isset($this->app->user->account) ? $this->app->user->account : '';
        if(!empty($this->config->db->name)) $config = $this->loadModel('setting')->getSysAndPersonalConfig($account);
        $this->config->system   = isset($config['system']) ? $config['system'] : new stdclass();
        $this->config->personal = isset($config[$account]) ? $config[$account] : new stdclass();

        foreach($this->config->system as $module => $records)
        {
            /* Overide the items defined in config/config.php and config/my.php. */
            if(!isset($this->config->$module)) $this->config->$module = new stdclass();
            if(isset($this->config->system->$module)) helper::mergeConfig($this->config->system->$module, $module);
        }

        foreach($this->config->personal as $module => $records)
        {
            /* Overide the items defined in config/config.php and config/my.php. */
            if(!isset($this->config->$module)) $this->config->$module = new stdclass();
            if(isset($this->config->personal->$module)) helper::mergeConfig($this->config->personal->$module, $module);
        }
        /* Overide the items defined in config/config.php and config/my.php. */
        if(isset($this->config->system->common)) helper::mergeConfig($this->config->system->common, 'common');
        if(isset($this->config->personal->common)) helper::mergeConfig($this->config->personal->common, 'common');
    }

    /**
     * Load custom lang from DB.
     * 
     * @access public
     * @return void
     */
    public function loadLangFromDB()
    {   
        if(!$this->config->db->name) return;
        $records = $this->loadModel('setting')->getAllLang();
        if(!$records) return;
        $this->lang->db = new stdclass();
        $this->lang->db->custom = $records;
    }   

    /**
     * Start the session.
     * 
     * @access public
     * @return void
     */
    public function startSession()
    {
        if(!defined('SESSION_STARTED'))
        {
            $sessionName = $this->config->sessionVar;
            if(isset($_GET[$sessionName])) session_id($_GET[$sessionName]);
            session_name($sessionName);
            session_start();
            define('SESSION_STARTED', true);
        }
    }

    /**
     * Check the Privilege.
     * 
     * @access public
     * @return void
     */
    public function checkPriv()
    {
        if(!empty($this->config->group->unUpdatedAccounts) and strpos($this->config->group->unUpdatedAccounts, $this->app->user->account) !== false)
        {
            $user = $this->app->user;
            $user->rights = $this->loadModel('user')->authorize($user);
            $this->session->set('user', $user);
            $this->app->user = $this->session->user;

            $unUpdatedAccounts = str_replace($this->app->user->account, '', $this->config->group->unUpdatedAccounts);
            if(str_replace(',', '', $unUpdatedAccounts) == '') $unUpdatedAccounts = '';
            $this->loadModel('setting')->setItem("system.sys.group.unUpdatedAccounts", $unUpdatedAccounts);
        }

        $module = $this->app->getModuleName();
        $method = $this->app->getMethodName();

        if($this->isOpenMethod($module, $method)) return true;

        /* Try to identify by cookie if not login. */
        if(!$this->loadModel('user')->isLogon() and $this->cookie->keepLogin == 'on') $this->user->identifyByCookie();

        /* If no $app->user yet, go to the login pae. */
        if($this->app->user->account == 'guest')
        {
            $referer  = helper::safe64Encode($this->app->getURI(true));
            die(js::locate(helper::createLink('sys.user', 'login', "referer=$referer")));
        }

        /* Check the Privilege. */
        if(!commonModel::hasPriv($module, $method)) $this->deny($module, $method);
    }

    /**
     * Check current user has Privilege to the module's method or not.
     * 
     * @param mixed $module     the module
     * @param mixed $method     the method
     * @static
     * @access public
     * @return bool
     */
    public static function hasPriv($module, $method)
    {
        global $app, $config, $lang;

        $appName = '';
        if(strpos($module, '.') !== false) list($appName, $module) = explode('.', $module);

        if(!commonModel::isAvailable($module)) return false;

        if($app->user->admin == 'super') return true;

        if(RUN_MODE == 'admin')
        {
            if($app->user->admin != 'super') return false;
        }

        /* Check app priv. */
        if(!commonModel::hasAppPriv($appName)) return false;

        $rights = $app->user->rights;
        //a($rights);exit;
        if(isset($rights[strtolower($module)][strtolower($method)])) return true;

        return false;
    }

    /**
     * Check current user has Privilege to the app or not.
     * 
     * @param  string $appname 
     * @static
     * @access public
     * @return bool
     */
    public static function hasAppPriv($appname = '')
    { 
        global $app;
        if(empty($appname)) $appname = $app->getAppName();

        if($appname == 'sys') return true;

        if($app->user->admin == 'super') return true;
        if(RUN_MODE == 'admin')
        {
            if($app->user->admin != 'super') return false;
        }

        $rights = $app->user->rights;
        /* Check app priv. */
        if(isset($rights['apppriv'][strtolower($appname)])) return true;

        return false;
    }

    /**
     * Check whether IP in white list.
     *
     * @access public
     * @return bool
     */
    public function checkIP()
    {
        $ip = $this->server->remote_addr;

        $ipWhiteList = $this->config->ipWhiteList;

        /* If the ip white list is '*'. */
        if($ipWhiteList == '*') return true;

        /* The ip is same as ip in white list. */
        if($ip == $ipWhiteList) return true;

        /* If the ip in white list is like 192.168.1.1-192.168.1.10. */
        if(strpos($ipWhiteList, '-') !== false)
        {
            list($min, $max) = explode('-', $ipWhiteList);
            $min = ip2long(trim($min));
            $max = ip2long(trim($max));
            $ip  = ip2long(trim($ip));

            return $ip >= $min and $ip <= $max;
        }

        /* If the ip in white list is in IP/CIDR format eg 127.0.0.1/24. Thanks to zcat. */
        if(strpos($ipWhiteList, '/') == false) $ipWhiteList .= '/32';
        list($ipWhiteList, $netmask) = explode('/', $ipWhiteList, 2);

        $ip          = ip2long($ip);
        $ipWhiteList = ip2long($ipWhiteList);
        $wildcard    = pow(2, (32 - $netmask)) - 1;
        $netmask     = ~ $wildcard;

        return (($ip & $netmask) == ($ipWhiteList & $netmask));
    }

    /**
     * Judge a module is available. 
     * 
     * @param  string $module 
     * @static
     * @access public
     * @return bool
     */
    public static function isAvailable($module)
    {
        global $config, $lang;
        if(isset($lang->setting->moduleList[$module]) and strpos($config->setting->modules, $module) === false) return false;
        return true;
    }

    /**
     * Check Privilege by customer.
     * 
     * @param  int    $customerID 
     * @param  string $type 
     * @access public
     * @return bool
     */
    public function checkPrivByCustomer($customerID, $type = 'view')
    {
        $customers = $this->loadModel('customer')->getCustomersSawByMe($type);
        if(!in_array($customerID, $customers))
        {
            $locate = helper::safe64Encode(helper::createLink('crm.index'));
            $noticeLink = helper::createLink('notice', 'index', "type=accessLimited&locate={$locate}");
            die(js::locate($noticeLink));
        }
    }

    /**
     * Show the deny info.
     * 
     * @param mixed $module     the module
     * @param mixed $method     the method
     * @access public
     * @return void
     */
    public function deny($module, $method)
    {
        if(helper::isAjaxRequest()) exit;

        /* Get authorize again. */
        $user = $this->app->user;
        $user->rights = $this->loadModel('user')->authorize($user);
        $this->session->set('user', $user);
        $this->app->user = $this->session->user;
        if(commonModel::hasPriv($module, $method)) return true;

        $vars = "module=$module&method=$method";
        if(isset($_SERVER['HTTP_REFERER']))
        {
            $referer  = helper::safe64Encode($_SERVER['HTTP_REFERER']);
            $vars .= "&referer=$referer";
        }
        $denyLink = helper::createLink('user', 'deny', $vars);
        die(js::locate($denyLink));
    }

    /** 
     * Judge a method of one module is open or not?
     * 
     * @param  string $module 
     * @param  string $method 
     * @access public
     * @return bool
     */
    public function isOpenMethod($module, $method)
    {   
        if($module == 'user' and strpos(',login|logout|deny|control', $method)) return true;
        if($module == 'api'  and $method == 'getsessionid') return true;
        if($module == 'misc' and $method == 'ping') return true;
        if($module == 'misc' and $method == 'ignorenotice') return true;
        if($module == 'action' and $method == 'read') return true;
        if($module == 'block') return true;
        if($module == 'notice') return true;
        if($module == 'sso' and strpos(',auth|check|gettodolist', $method)) return true;
        if($module == 'attend' and strpos(',signin|signout', $method)) return true;
        if($module == 'refund' and $method == 'createtrade') return true;
        if($module == 'file'   and $method == 'read') return true;

        if($this->loadModel('user')->isLogon() and stripos($method, 'ajax') !== false) return true;

        return false;
    }   

    /**
     * Create the main menu.
     * 
     * @param  string $currentModule 
     * @static
     * @access public
     * @return string
     */
    public static function createMainMenu($currentModule)
    {
        global $app, $lang, $config;

        /* Set current module. */
        if(isset($lang->menuGroups->$currentModule)) $currentModule = $lang->menuGroups->$currentModule;

        $isMobile = $app->viewType === 'mhtml';
        $string   = !$isMobile ? "<ul class='nav navbar-nav'>\n" : '';

        $menuOrder = isset($lang->{$app->appName}->menuOrder) ? $lang->{$app->appName}->menuOrder : array();  
        $allMenus  = new stdclass(); 
        if(!empty($menuOrder))
        {
            ksort($menuOrder);
            foreach($lang->menu->{$app->appName} as $moduleName => $moduleMenu)
            {
                if(!in_array($moduleName, $menuOrder)) $menuOrder[] = $moduleName;
            }

            foreach($menuOrder as $name)
            {
                if(isset($lang->menu->{$app->appName}->$name)) $allMenus->$name = $lang->menu->{$app->appName}->$name;
            }

            foreach($lang->menu->{$app->appName} as $key => $value)
            {
                if(!isset($allMenus->$key)) $allMenus->$key = $value;
            }
        }
        else
        {
            $allMenus = $lang->menu->{$app->appName};
        }

        /* Print all main menus. */
        foreach($allMenus as $moduleName => $moduleMenu)
        {
            $class = $moduleName == $currentModule ? " class='active'" : '';
            list($label, $module, $method, $vars) = explode('|', $moduleMenu);

            if(isset($config->attend->noAttendUsers) and strpos(",{$config->attend->noAttendUsers},", ",{$app->user->account},") !== false and $module == 'attend' and $method == 'personal')
            {
                $method = 'department';
            }

            if(!commonModel::isAvailable($module)) continue;

            if(strpos(',tree,setting,schema,sales,', ',' . $module . ',') != false and isset($lang->setting->menu)) 
            {
                foreach($lang->setting->menu as $settingMenu)
                {
                    $class = $currentModule == 'setting' ? " class='active'" : '';
                    if(is_array($settingMenu)) $settingMenu = $settingMenu['link'];
                    list($settingLabel, $moduleName, $methodName, $settingVars) = explode('|', $settingMenu);

                    if(commonModel::hasPriv($moduleName, $methodName))
                    {
                        $link  = helper::createLink($moduleName, $methodName, $settingVars);
                        $string .= !$isMobile ? "<li$class><a href='$link'>$label</a></li>\n" : "<a$class href='$link'>$label</a>";
                        break;
                    }
                }
            }
            else
            {
                if(commonModel::hasPriv($module, $method))
                {
                    $link  = helper::createLink($module, $method, $vars);
                    $string .= !$isMobile ? "<li$class><a href='$link'>$label</a></li>\n" : "<a$class href='$link'>$label</a>";
                }
            }
        }

        $string .= !$isMobile ? "</ul>\n" : '';
        return $string;
    }

    /**
     * Create the module menu.
     * 
     * @param  string $currentModule 
     * @static
     * @access public
     * @return void
     */
    public static function createModuleMenu($currentModule)
    {
        global $lang, $app, $config;

        /* Get current method. */
        $currentMethod = $app->getMethodName();

        if(!isset($lang->$currentModule->menu)) return false;
        if(isset($config->attend->noAttendUsers) and strpos(",{$config->attend->noAttendUsers},", ",{$app->user->account},") !== false and isset($lang->attend->menu->personal))
        {
            unset($lang->attend->menu->personal);
        }

        $isMobile = $app->viewType === 'mhtml';
        $string   = !$isMobile ? "<nav id='menu'><ul class='nav'>\n" : '';
        if(!$isMobile && strpos(',setting,tree,schema,sales,group,', ",$currentModule,") !== false) 
        {
            $string = "<nav class='menu leftmenu affix'><ul class='nav nav-primary'>\n";
            if($currentModule == 'setting' && strpos(',ameba,psi,xuanxuan,modules,', ",$currentMethod,") !== false)
            {
                $string = "<nav id='menu'><ul class='nav'>\n";
            }
        }

        if(!$isMobile && $currentModule == 'trade' && $currentMethod == 'tradesetting')
        {
            $string = "<nav class='menu leftmenu affix'><ul class='nav nav-primary'>\n";
        }

        $menuOrder = isset($lang->{$currentModule}->menuOrder) ? $lang->{$currentModule}->menuOrder : array();  

        /* Get menus of current module. */
        $moduleMenus = new stdclass(); 
        if(!empty($menuOrder))
        {
            ksort($menuOrder);
            foreach($menuOrder as $name)
            {
                if(isset($lang->{$currentModule}->menu->$name)) $moduleMenus->$name = $lang->{$currentModule}->menu->$name;
            }

            foreach($lang->{$currentModule}->menu as $key => $value)
            {
                if(!isset($moduleMenus->$key)) $moduleMenus->$key = $value;
            }
        }
        else
        {
            $moduleMenus = $lang->$currentModule->menu;  
        }

        /* Cycling to print every menus of current module. */
        foreach($moduleMenus as $methodName => $methodMenu)
        {
            if(is_array($methodMenu)) 
            {
                $methodAlias = isset($methodMenu['alias']) ? $methodMenu['alias'] : '';
                $methodLink  = $methodMenu['link'];
            }
            else
            {
                $methodAlias = '';
                $methodLink  = $methodMenu;
            }

            /* Split the methodLink to label, module, method, vars. */
            list($label, $module, $method, $vars) = explode('|', $methodLink);

            if(commonModel::hasPriv($module, $method))
            {
                if($module == 'trade' and $method == 'browse' and $vars == 'mode=out')
                {
                    if(!commonModel::hasPriv('tradebrowse', 'out')) continue;
                }

                $class = '';
                if($module == $currentModule && strtolower($method) == $currentMethod) $class = " class='active'";
                if($module == $currentModule && stripos($methodAlias, $currentMethod) !== false) $class = " class='active'";
                $url  = helper::createLink($module, $method, $vars);
                $link = html::a($url, $label);
                if(strpos($string, "class='active'") != false)
                {

                    $string .= !$isMobile ? ("<li>$link</li>\n") : $link;
                }
                else
                {
                    $string .= !$isMobile ? "<li{$class}>$link</li>\n" : html::a($url, $label, $class);
                }
            }
        }

        $string .= !$isMobile ? "</ul></nav>\n" : '';
        return $string;
    }

    /**
     * Create menu of dashboard.
     * 
     * @static
     * @access public
     * @return string
     */
    public static function createDashboardMenu()
    {
        global $app, $lang;

        $isMobile = $app->viewType === 'mhtml';
        $string   = !$isMobile ? "<ul class='nav navbar-nav'>\n" : '';

        $menuOrder = isset($lang->sys->dashboard->menuOrder) ? $lang->sys->dashboard->menuOrder : array();  
        $allMenus  = new stdclass(); 
        if(!empty($menuOrder))
        {
            ksort($menuOrder);
            foreach($lang->menu->dashboard as $moduleName => $moduleMenu)
            {
                if(!in_array($moduleName, $menuOrder)) $menuOrder[] = $moduleName;
            }

            foreach($menuOrder as $name)
            {
                if(isset($lang->menu->dashboard->$name)) $allMenus->$name = $lang->menu->dashboard->$name;
            }

            foreach($lang->menu->dashboard as $key => $value)
            {
                if(!isset($allMenus->$key)) $allMenus->$key = $value;
            }
        }
        else
        {
            $allMenus = $lang->menu->dashboard;
        }

        $currentMethod = $app->getMethodName();
        $currentModule = $app->getModuleName();
        foreach($allMenus as $moduleName => $moduleMenu)
        {
            list($label, $module, $method, $vars) = explode('|', $moduleMenu);

            $class = '';
            if($currentMethod == strtolower($method) or ($currentModule == 'todo' and $module == 'todo')) $class = "class='active'";
            $hasPriv = commonModel::hasPriv($module, $method);
            if($module == 'my' and $method == 'order')    $hasPriv = commonModel::hasPriv('order', 'browse');
            if($module == 'my' and $method == 'contract') $hasPriv = commonModel::hasPriv('contract', 'browse');

            if($module == 'my' and $method == 'review')
            {
                $hasPriv = false;
                foreach($lang->my->review->menu as $methodName => $methodMenu)
                {
                    if(commonModel::hasPriv($methodName, 'review'))
                    {
                        $hasPriv = true;
                        break;
                    }
                }
            }

            if($hasPriv)
            {
                $link = helper::createLink($module, $method, $vars);
                if(!$isMobile)
                {
                    $string .= "<li $class><a class='app-btn open' data-id='dashboard' href='$link'>$label</a></li>\n";
                }
                else
                {
                    $string .= "<a $class href='$link'>$label</a>\n";
                }
            }
        }

        $string .= !$isMobile ? "</ul>\n" : '';
        return $string;
    }

    /**
     * Create menu for managers.
     * 
     * @access public
     * @return string
     */
    public static function createManagerMenu()
    {
        global $app, $lang , $config;

        $string  = '<ul class="nav navbar-nav navbar-right">';
        $string .= sprintf('<li>%s</li>', html::a($config->webRoot, '<i class="icon-home icon-large"></i> ' . $lang->frontHome, "target='_blank' class='navbar-link'"));
        $string .= sprintf('<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user icon-large"></i> %s <b class="caret"></b></a>', $app->user->realname);
        $string .= sprintf('<ul class="dropdown-menu"><li>%s</li><li>%s</li></ul>', html::a(helper::createLink('user', 'changePassword'), $lang->changePassword, "data-toggle='modal'"), html::a(helper::createLink('user','logout'), $lang->logout));
        $string .= '</li></ul>';

        return $string;
    }

    /**
     * Print position bar 
     *
     * @param   object $module 
     * @param   object $object 
     * @param   mixed  $misc    other params. 
     * @access  public
     * @return  void
     */
    public function printPositionBar($module = '', $object = '', $misc = '', $root = '')
    {
        echo '<ul class="breadcrumb">';
        if($root == '')
        {
            echo '<li>' . $this->lang->currentPos . $this->lang->colon . html::a(helper::createLink('index'), strtoupper($this->app->appName)) . '</li>';
        }
        else
        {
            echo $root;
        }

        $moduleName = $this->app->getModuleName();
        $moduleName = $moduleName == 'reply' ? 'thread' : $moduleName;
        $funcName = "print$moduleName";
        if(method_exists('commonModel', $funcName)) echo $this->$funcName($module, $object, $misc);
        echo '</ul>';
    }

    /**
     * Print the link contains orderBy field.
     *
     * This method will auto set the orderby param according the params. For example, if the order by is desc,
     * will be changed to asc.
     *
     * @param  string $fieldName    the field name to sort by
     * @param  string $orderBy      the order by string
     * @param  string $vars         the vars to be passed
     * @param  string $label        the label of the link
     * @param  string $module       the module name
     * @param  string $method       the method name
     * @static
     * @access public
     * @return void
     */
    public static function printOrderLink($fieldName, $orderBy, $vars, $label, $module = '', $method = '')
    {
        global $lang, $app;
        if(empty($module)) $module = $app->getModuleName();
        if(empty($method)) $method = $app->getMethodName();
        $className = '';
        $isMobile  = $app->viewType === 'mhtml';

        if(strpos($orderBy, $fieldName) !== false)
        {
            if(stripos($orderBy, 'desc') !== false)
            {
                $orderBy   = str_ireplace('desc', 'asc', $orderBy);
                $className = 'SortDown';
            }
            elseif(stripos($orderBy, 'asc')  !== false)
            {
                $orderBy = str_ireplace('asc', 'desc', $orderBy);
                $className = 'SortUp';
            }
        }
        else
        {
            $orderBy   = $fieldName . '_' . 'asc';
            $className = '';
        }

        $link = helper::createLink($module, $method, sprintf($vars, $orderBy));

        if(!$isMobile) echo "<div class='header$className'>" . html::a($link, $label) . '</div>';
        else echo html::a($link, $label, "class='$className'");
    }
 
    /**
     * Set the user info.
     * 
     * @access public
     * @return void
     */
    public function setUser()
    {
        if($this->session->user) return $this->app->user = $this->session->user;

        /* Create a guest account. */
        $user           = new stdclass();
        $user->id       = 0;
        $user->account  = 'guest';
        $user->realname = 'guest';
        $user->admin    = RUN_MODE == 'cli' ? 'super' : 'no';
        $user->rights   = array();

        $this->session->set('user', $user);
        $this->app->user = $this->session->user;
    }

    /**
     * Set the entry info.
     * 
     * @access public
     * @return void
     */
    public function setEntry()
    {
        $entry = $this->dao->select('*')->from(TABLE_ENTRY)->where('code')->eq($this->appName)->fetch();
        $this->session->set('entry', $entry);
        $this->app->entry = $this->session->entry;
    }

    /**
     * Get the run info.
     * 
     * @param mixed $startTime  the start time of this execution
     * @access public
     * @return array    the run info array.
     */
    public function getRunInfo($startTime)
    {
        $info['timeUsed'] = round(getTime() - $startTime, 4) * 1000;
        $info['memory']   = round(memory_get_peak_usage() / 1024, 1);
        $info['querys']   = count(dao::$querys);
        return $info;
    }

    /**
     * Print top bar.
     * 
     * @static
     * @access public
     * @return void
     */
    public static function printTopBar()
    {
        global $lang, $app;

        printf($lang->todayIs, date(DT_DATE4));
        if(isset($app->user)) echo $app->user->realname . ' ';
        if(isset($app->user) and $app->user->account != 'guest')
        {
            echo html::a(helper::createLink('user', 'logout'), $lang->logout);
        }
        else
        {
            echo html::a(helper::createLink('user', 'login'), $lang->login);
        }

        echo '&nbsp;|&nbsp; ';
        echo html::a(helper::createLink('misc', 'about'), $lang->about, '', "class='about'");

        echo '&nbsp;|&nbsp;';
        echo html::select('', $app->config->langs, $app->cookie->lang,  'onchange="selectLang(this.value)"');
    }

    /**
     * Print the main menu.
     * 
     * @param  string $moduleName 
     * @static
     * @access public
     * @return void
     */
    public static function printMainmenu($moduleName, $methodName = '')
    {
        global $app, $lang;
        echo "<ul>\n";
 
        /* Set the main main menu. */
        $mainMenu = $moduleName;
        if(isset($lang->menuGroups->$moduleName)) $mainMenu = $lang->menuGroups->$moduleName;

        $activeName = $app->getViewType() == 'mhtml' ? 'ui-btn-active' : 'active';

        /* Print all main menus. */
        foreach($lang->menu->{$app->appName} as $menuKey => $menu)
        {
            $active = $menuKey == $mainMenu ? "class='$activeName'" : '';
            $link = explode('|', $menu);
            list($menuLabel, $module, $method) = $link;
            $vars = isset($link[3]) ? $link[3] : '';

            if(commonModel::hasPriv($module, $method))
            {
                $link  = helper::createLink($module, $method, $vars);
                echo "<li $active><a href='$link' $active id='menu$menuKey'>$menuLabel</a></li>\n";
            }
        }

    }

    /**
     * Print the module menu.
     * 
     * @param  string $moduleName 
     * @static
     * @access public
     * @return void
     */
    public static function printModuleMenu($moduleName)
    {
        global $lang, $app;

        if(!isset($lang->$moduleName->menu)) {echo "<ul></ul>"; return;}

        /* Get the sub menus of the module, and get current module and method. */
        $submenus      = $lang->$moduleName->menu;  
        $currentModule = $app->getModuleName();
        $currentMethod = $app->getMethodName();

        /* Sort the subMenu according to menuOrder. */
        if(isset($lang->$moduleName->menuOrder))
        {
            $menus = $submenus;
            $submenus = new stdclass();

            ksort($lang->$moduleName->menuOrder, SORT_ASC);
            if(isset($menus->list)) 
            {
                $submenus->list = $menus->list; 
                unset($menus->list);
            }
            foreach($lang->$moduleName->menuOrder as $order)  
            {
                if(($order != 'list') && isset($menus->$order))
                {
                    $subOrder = $menus->$order;
                    unset($menus->$order);
                    $submenus->$order = $subOrder;
                }
            }
            foreach($menus as $key => $menu)
            {
                $submenus->$key = $menu; 
            }
        }

        /* The beginning of the menu. */
        echo "<ul>\n";

        /* Cycling to print every sub menus. */
        foreach($submenus as $subMenuKey => $submenu)
        {
            /* Init the these vars. */
            $link      = $submenu;
            $subModule = '';
            $alias     = '';
            $float     = '';
            $active    = '';
            $target    = '';

            if(is_array($submenu)) extract($submenu);   // If the sub menu is an array, extract it.

            /* Print the menu. */
            if(strpos($link, '|') === false)
            {
                echo "<li>$link</li>\n";
            }
            else
            {
                $link = explode('|', $link);
                list($label, $module, $method) = $link;
                $vars = isset($link[3]) ? $link[3] : '';
                if(commonModel::hasPriv($module, $method))
                {
                    /* Is the currentModule active? */
                    $subModules = explode(',', $subModule);
                    if(in_array($currentModule,$subModules) and $float != 'right') $active = 'active';
                    if($module == $currentModule and ($method == $currentMethod or strpos(",$alias,", ",$currentMethod,") !== false) and $float != 'right') $active = 'active';
                    echo "<li class='$float $active'>" . html::a(helper::createLink($module, $method, $vars), $label, $target, "id=submenu$subMenuKey") . "</li>\n";
                }
            }
        }
        echo "</ul>\n";
    }

    /**
     * Print the bread menu.
     * 
     * @param  string $moduleName 
     * @param  string $position 
     * @static
     * @access public
     * @return void
     */
    public static function printBreadMenu($moduleName, $position)
    {
        global $lang;
        $mainMenu = $moduleName;
        if(isset($lang->menuGroups->$moduleName)) $mainMenu = $lang->menuGroups->$moduleName;
        echo html::a(helper::createLink('my', 'index'), $lang->ranzhi);
        if($moduleName != 'index')
        {
            if(!isset($lang->menu->$mainMenu)) return;
            list($menuLabel, $module, $method) = explode('|', $lang->menu->$mainMenu);
            echo html::a(helper::createLink($module, $method), $menuLabel);
        }
        else
        {
            echo $lang->index->common;
        }
        if(empty($position)) return;
        echo $lang->arrow;
        foreach($position as $key => $link)
        {
            echo $link;
            if(isset($position[$key + 1])) echo $lang->arrow;
        }
    }

    /**
     * Print the link for notify file.
     * 
     * @static
     * @access public
     * @return void
     */
    public static function printNotifyLink()
    {
        if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
        {
            global $app, $lang;
            $notifyFile = $app->getBasePath() . 'www/data/notify/notify.zip';

            if(!file_exists($notifyFile)) return false;
            echo html::a(helper::createLink('misc', 'downNotify'), $lang->downNotify);
        }
    }

    /**
     * Get the full url of the system.
     * 
     * @static
     * @access public
     * @return string
     */
    public static function getSysURL()
    {
        $httpType = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on' ? 'https' : 'http';
        $httpHost = $_SERVER['HTTP_HOST'];
        return "$httpType://$httpHost";
    }

    /**
     * Get client IP.
     * 
     * @access public
     * @return void
     */
    public function getIP()
    {
        if(getenv("HTTP_CLIENT_IP"))
        {
            $ip = getenv("HTTP_CLIENT_IP");
        }
        elseif(getenv("HTTP_X_FORWARDED_FOR"))
        {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        }
        elseif(getenv("REMOTE_ADDR"))
        {
            $ip = getenv("REMOTE_ADDR");
        }
        else
        {
            $ip = "Unknow";
        }

        return $ip;
    }

    /**
     * Print the position bar of thread module.
     * 
     * @param   object $board 
     * @param   object $thread 
     * @access  public
     * @return  void
     */
    public function printForum($board, $thread = '')
    {
        echo '<ul class="breadcrumb">';
        echo '<li>' . html::a(helper::createLink('index'), strtoupper($this->app->appName)) . '</li>';
        $divider = $this->lang->divider;
        echo '<li>' . html::a(helper::createLink('forum', 'index'), $this->lang->forumHome) . '</li>';
        if(!$board) return false;

        unset($board->pathNames[key($board->pathNames)]);
        foreach($board->pathNames as $boardID => $boardName)
        {
            echo '<li>' . html::a(helper::createLink('forum', 'board', "boardID={$boardID}"), $boardName) . '</li>';
        }
        if($thread) echo '<li>' . $thread->title . '</li>';
        echo '</ul>';
    }

    /**
     * Create front link for admin MODEL.
     *
     * @param string       $module
     * @param string       $method
     * @param string|array $vars
     * @param string|array $alias
     * return string 
     */
    public static function createFrontLink($module, $method, $vars = '', $alias = '')
    {
        if(RUN_MODE == 'front') return helper::createLink($module, $method, $vars, $alias);

        global $config;

        $config->requestType = $config->frontRequestType;
        $link = helper::createLink($module, $method, $vars, $alias);
        $link = str_replace('admin.php', 'index.php', $link);
        $config->requestType = 'GET';

        return $link;
    }
 
    /**
     * Verify administrator through ok file.
     * 
     * @access public
     * @return array
     */
    public function verifyAdmin()
    {
        $okFile = $this->app->getWwwRoot() . 'ok.txt';
        if(!file_exists($okFile) or time() - filemtime($okFile) > 3600)
        {
            return array('result' => 'fail', 'okFile' => $okFile);
        }

        return array('result' => 'success');
    }

    /**
     * Create changes of one object.
     * 
     * @param mixed $old    the old object
     * @param mixed $new    the new object
     * @static
     * @access public
     * @return array
     */
    public static function createChanges($old, $new)
    {   
        global $config;
        $changes    = array();
        $magicQuote = get_magic_quotes_gpc();
        foreach($new as $key => $value)
        {   
            if(!isset($old->$key))                   continue;
            if(strtolower($key) == 'lastediteddate') continue;
            if(strtolower($key) == 'lasteditedby')   continue;
            if(strtolower($key) == 'assigneddate')   continue;
            if(strtolower($key) == 'editedby')       continue;
            if(strtolower($key) == 'editeddate')     continue;

            if(is_array($value))
            {
                if(is_string(reset($value))) $value = join(',', $value);
                else $value = join(',', array_keys($value)); 
            }
            if(is_array($old->$key)) 
            {
                if(is_string(reset($old->$key))) $old->$key = join(',', $old->$key);
                else $old->$key = join(',', array_keys($old->$key)); 
            }

            if($magicQuote) $value = stripslashes($value);
            if($value != stripslashes($old->$key))
            {
                $diff = '';
                if(substr_count($value, "\n") > 1     or
                   substr_count($old->$key, "\n") > 1 or
                   strpos(',name,title,desc,content,summary,', ',' . strtolower($key) . ',') !== false)
                {
                    $diff = commonModel::diff($old->$key, $value);
                }
                $changes[] = array('field' => $key, 'old' => $old->$key, 'new' => $value, 'diff' => $diff);
            }
        }
        return $changes;
    }

    /**
     * Diff two string. (see phpt)
     * 
     * @param string $text1 
     * @param string $text2 
     * @static
     * @access public
     * @return string
     */
    public static function diff($text1, $text2)
    {
        $text1 = str_replace('&nbsp;', '', trim($text1));
        $text2 = str_replace('&nbsp;', '', trim($text2));
        $w  = explode("\n", $text1);
        $o  = explode("\n", $text2);
        $w1 = array_diff_assoc($w,$o);
        $o1 = array_diff_assoc($o,$w);
        $w2 = array();
        $o2 = array();
        foreach($w1 as $idx => $val) $w2[sprintf("%03d<",$idx)] = sprintf("%03d- ", $idx+1) . "<del>" . trim($val) . "</del>";
        foreach($o1 as $idx => $val) $o2[sprintf("%03d>",$idx)] = sprintf("%03d+ ", $idx+1) . "<ins>" . trim($val) . "</ins>";
        $diff = array_merge($w2, $o2);
        ksort($diff);
        return implode("\n", $diff);
    }

    /**
     * Print backLink and preLink and nextLink.
     * 
     * @param  string $backLink 
     * @param  object $preAndNext 
     * @access public
     * @return void
     */
    static public function printRPN($backLink, $preAndNext = '', $linkTemplate = '')
    {
        global $lang, $app;
        if(isonlybody()) return false;

        echo html::a($backLink, $lang->goback, "class='btn btn-default' id='backButton'");

        if(isset($preAndNext->pre) and $preAndNext->pre) 
        {
            $id = 'id';
            $title = isset($preAndNext->pre->title) ? $preAndNext->pre->title : (isset($preAndNext->pre->name) ? $preAndNext->pre->name : '');
            $title = '#' . $preAndNext->pre->$id . ' ' . $title;
            $link  = $linkTemplate ? sprintf($linkTemplate, $preAndNext->pre->$id) : inlink('view', "ID={$preAndNext->pre->$id}");
            echo html::a($link, '<i class="icon-pre icon-chevron-left"></i>', "id='pre' class='btn' title='{$title}'");
        }
        if(isset($preAndNext->next) and $preAndNext->next) 
        {
            $id = 'id';
            $title = isset($preAndNext->next->title) ? $preAndNext->next->title : (isset($preAndNext->next->name) ? $preAndNext->next->name : '');
            $title = '#' . $preAndNext->next->$id . ' ' . $title;
            $link  = $linkTemplate ? sprintf($linkTemplate, $preAndNext->next->$id) : inlink('view', "ID={$preAndNext->next->$id}");
            echo html::a($link, '<i class="icon-pre icon-chevron-right"></i>', "id='next' class='btn' title='$title'");
        }
    }

    /**
     * Get the previous and next object.
     * 
     * @param  string $type story|task|bug|case
     * @param  string $objectIDs 
     * @param  string $objectID 
     * @access public
     * @return void
     */
    public function getPreAndNextObject($type, $objectID)
    {
        $preAndNextObject = new stdClass();

        if(strpos(',order,contract,customer,contact,task,thread,blog,refund,trade,', ",$type,") === false) return $preAndNextObject;
        $table = $this->config->objectTables[$type];

        $queryCondition = "{$type}QueryCondition";
        $queryCondition = $this->session->$queryCondition;
        if(!$queryCondition) return $preAndNextObject;

        /* delete limit condition if exist. */
        if(stripos($queryCondition, 'limit')) $queryCondition = substr($queryCondition, 0, stripos($queryCondition, 'limit'));
        $queryObjects = $this->dao->query($queryCondition);

        $preOBJ  = false;
        $preAndNextObject->pre  = '';
        $preAndNextObject->next = '';
        while($object = $queryObjects->fetch())
        {
            $id = $object->id;

            /* Get next object. */
            if($preOBJ === true)
            {
                $preAndNextObject->next = $object;
                break;
            }

            /* Get pre object. */
            if($id == $objectID)
            {
                if($preOBJ) $preAndNextObject->pre = $preOBJ;
                $preOBJ = true;
            }
            if($preOBJ !== true) $preOBJ = $object;
        }

        return $preAndNextObject;
    }

    /**
     * Print link to an modules' methd.
     *
     * Before printing, check the privilege first. If no privilege, return fasle. Else, print the link, return true.
     * 
     * @param  string $module   the module name
     * @param  string $method   the method
     * @param  string $vars     vars to be passed
     * @param  string $label    the label of the link
     * @param  string $misc     others
     * @param  bool   $print 
     * @param  bool   $onlyBody 
     * @param  string $type     li
     * @static
     * @access public
     * @return bool
     */
    public static function printLink($module, $method, $vars = '', $label, $misc = '', $print = true, $onlyBody = false, $type = '')
    {
        if(!commonModel::hasPriv($module, $method)) return false;

        $content  = '';
        $canClick = commonModel::checkPrivByVars($module, $method, $vars);
        $link     = helper::createLink($module, $method, $vars, '', $onlyBody);
        if(!$canClick)
        {
            $misc = str_replace("class='", "disabled='disabled' class='disabled ", $misc);
            $misc = str_replace("data-toggle='modal'", ' ', $misc);
            $misc = str_replace("deleter", ' ', $misc);
            if(strpos($misc, "class='") === false) $misc .= " class='disabled' disabled='disabled'";
        }
        if($type == 'li') $content .= '<li' . ($canClick ? '' : " disabled='disabled' class='disabled'") . '>';
        $content .= html::a($canClick ? $link : 'javascript:void(0)', $label, $misc);
        if($type == 'li') $content .= '</li>';

        if($print !== false) echo $content;
        return $content;
    }

    /**
     * Check privilege by vars. 
     * 
     * @param  string $module 
     * @param  string $method 
     * @param  string|array $vars 
     * @static
     * @access public
     * @return void
     */
    public static function checkPrivByVars($module, $method, $vars)
    {
        global $app;
        if(!is_array($vars)) parse_str($vars, $vars);
        if(strpos($module, '.') !== false) $module = substr($module, strpos($module, '.') + 1);
        $method = strtolower($method);

        /* Check priv by {$moduleName}ID. */
        $checkByID['customer'] = ',assign,edit,delete,linkcontact,';
        $checkByID['order']    = ',assign,edit,delete,close,activate,';
        $checkByID['contract'] = ',receive,delivery,edit,delete,finish,cancel,';
        $checkByID['resume']   = ',edit,delete,';
        $checkByID['address']  = ',edit,delete,';
        if($app->appName == 'crm') $checkByID['contact'] = ',edit,delete,';
        foreach($checkByID as $moduleName => $methodName)
        {
            if($module == $moduleName and strpos($methodName, ",$method,") !== false)
            {
                $idName     = "{$moduleName}ID";
                $idListName = 'canEdit' . ucwords($moduleName) . 'IdList';
                if(!isset($vars[$idName])) return false;
                $idList = isset($app->user->$idListName) ? $app->user->$idListName : '';
                if(strpos($idList, ",{$vars[$idName]},") === false) return false;
            }
        }

        /* Check priv by objectType and objectID. */
        $checkByType['action']  = ',createrecord,';
        $checkByType['address'] = ',create,';
        foreach($checkByType as $moduleName => $methodName)
        {
            if($module == $moduleName and strpos($methodName, ",$method,") !== false)
            {
                if(!isset($vars['objectType']) or !isset($vars['objectID'])) return false;
                $idName     = $vars['objectType'] . 'ID';
                $idListName = 'canEdit' . ucwords($vars['objectType']) . 'IdList';
                $idList     = isset($app->user->$idListName) ? $app->user->$idListName : '';
                return commonModel::checkPrivByVars($vars['objectType'], 'edit', "{$idName}={$vars['objectID']}");
            }
        }

        /* Check priv use another method. module|method */
        $checkByGroup['resume']['create'] = 'contact|edit';
        foreach($checkByGroup as $moduleName => $methodNames)
        {
            foreach($methodNames as $methodName => $settings)
            {
                list($newModuleName, $newMethodName) = explode('|', $settings);
                if($module == $moduleName and $method == $methodName)
                {
                    return commonModel::checkPrivByVars($newModuleName, $newMethodName, $vars);
                }
            }
        }

        return true;
    }

    /**
     * Get currency list.
     * 
     * @access public
     * @return array
     */
    public function getCurrencyList()
    {
        $currencyList = array();
        foreach($this->lang->currencyList as $key => $currency)
        {
            if(strpos($this->config->setting->currency, $key) === false) continue;
            $currencyList[$key] = $currency;
        }
        return $currencyList;
    }

    /**
     * Get currency sign.
     * 
     * @access public
     * @return array
     */
    public function getCurrencySign()
    {
        $currencySign = array();
        foreach($this->lang->currencySymbols as $key => $sign)
        {
            if(strpos($this->config->setting->currency, $key) === false) continue;
            $currencySign[$key] = $sign;
        }
        return $currencySign;
    }  

    /**
     * Sort entry by order. 
     * 
     * @param  object $a 
     * @param  object $b 
     * @access private
     * @return bool
     */
    public static function sortEntryByOrder($a, $b)
    {
        if($a->order == $b->order)
        {
            return $a->id > $b->id ? 1 : -1;
        }
        return $a->order > $b->order ? 1 : -1;
    }

    /**
     * Format money.
     * 
     * @param  float $money 
     * @static
     * @access public
     * @return string
     */
    public static function tidyMoney($money)
    {
        global $lang, $app;

        $clientLang = $app->getClientLang();
        if($clientLang == 'zh-cn' or $clientLang == 'zh-tw')
        {
            if($money > pow(10, 8))
            {
                return ((int)($money / pow(10, 6)) / 100) . $lang->currencyTip['y'];
            }
            else if($money > pow(10, 4))
            {
                return ((int)($money / pow(10, 2)) / 100) . $lang->currencyTip['w'];
            }
            else
            {
                return $money;
            }
        }
        else
        {
            return formatMoney($money);
        }
    } 

    /**
     * Http.
     * 
     * @param  string    $url 
     * @param  string    $data 
     * @static
     * @access public
     * @return string
     */
    public static function http($url, $data = null)
    {
        global $lang;
        if(!extension_loaded('curl')) return json_encode(array('result' => 'fail', 'message' => $lang->error->noCurlExt));

        $ci = curl_init();
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_USERAGENT, 'Sae T OAuth2 v0.1');
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_ENCODING, "");
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ci, CURLOPT_HEADER, FALSE);

        $headers[] = "API-RemoteIP: " . $_SERVER['REMOTE_ADDR'];
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
        if(!empty($data)) 
        {
            curl_setopt($ci, CURLOPT_POST, true);
            curl_setopt($ci, CURLOPT_POSTFIELDS, $data);
        }

        $response = curl_exec($ci);
        curl_close ($ci);

        commonModel::log($url, $response);
        return $response;
    }

    /**
     * Log.
     * 
     * @param  string    $url 
     * @param  string    $results 
     * @static
     * @access public
     * @return void
     */
    public static function log($url, $results)
    {
        global $app;
        $logFile = $app->getLogRoot() . 'saas.'. date('Ymd') . '.log';
        $fh = @fopen($logFile, 'a');
        if(!$fh) return false;

        fwrite($fh, date('Ymd H:i:s') . ": " . $app->getURI() . "\n");
        fwrite($fh, "url:    " . $url . "\n");
        fwrite($fh, "results:" . print_r($results, true));
        fwrite($fh, "\n");
        fclose($fh);
    }
}
