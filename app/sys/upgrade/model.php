<?php
/**
 * The model file of upgrade module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     upgrade
 * @version     $Id: model.php 4227 2016-10-25 08:27:56Z liugang $
 * @link        http://www.ranzhico.com
 */
?>
<?php
class upgradeModel extends model
{
    /**
     * Errors.
     * 
     * @static
     * @var array 
     * @access public
     */
    static $errors = array();

    /**
     * The execute method. According to the $fromVersion call related methods.
     * 
     * @param  string $fromVersion 
     * @access public
     * @return void
     */
    public function execute($fromVersion)
    {
        $result = array();
    
        /* Delete useless file.*/
        foreach($this->config->delete as $deleteFiles)
        {
            $basePath = $this->app->getBasePath();
            foreach($deleteFiles as $file)
            {
                $fullPath = $basePath . str_replace('/', DIRECTORY_SEPARATOR, $file);
                if(is_dir($fullPath)  and !rmdir($fullPath))  $result[] = sprintf($this->lang->upgrade->deleteDir, $fullPath);
                if(is_file($fullPath) and !unlink($fullPath)) $result[] = sprintf($this->lang->upgrade->deleteFile, $fullPath);
            }
        }
        if(!empty($result)) return array('' => $this->lang->upgrade->deleteTips) + $result;
    
        switch($fromVersion)
        {
            case '1_0_beta':
                $this->execSQL($this->getUpgradeFile('1.0.beta'));
                $this->createCashEntry();
            case '1_1_beta':
                $this->execSQL($this->getUpgradeFile('1.1.beta'));
                $this->createTeamEntry();
            case '1_2_beta':
                $this->execSQL($this->getUpgradeFile('1.2.beta'));
                $this->transformBlock();
                $this->changeBuildinName();
                $this->computeContactInfo();
            case '1_3_beta':
                $this->execSQL($this->getUpgradeFile('1.3.beta'));
                $this->setCompanyContent();
            case '1_4_beta':
                $this->upgradeContractName();
                $this->upgradeProjectMember();
                $this->safeDropColumns(TABLE_PROJECT, 'master,member');

                /* Exec sqls must after fix data. */
                $this->execSQL($this->getUpgradeFile('1.4.beta'));
            case '1_5_beta':
                $this->execSQL($this->getUpgradeFile('1.5.beta'));
                $this->upgradeEntryLogo();
                $this->upgradeReturnRecords();
                $this->upgradeDeliveryRecords();
                $this->addSearchPriv();
            case '1_6':
                $this->execSQL($this->getUpgradeFile('1.6'));
                $this->addPrivs();
            case '1_7':
                $this->toLowerTable();
                $this->updateAppOrder();
            case '2_0':
                $this->execSQL($this->getUpgradeFile('2.0'));
                $this->fixClosedTask();
                $this->setSalesGroup();
                $this->fixOrderProduct();
            case '2_1':
                $this->execSQL($this->getUpgradeFile('2.1'));
            case '2_2':
                $this->processTradeDesc();
            case '2_3':
                $this->processCustomerEditedDate();
                $this->execSQL($this->getUpgradeFile('2.3'));
            case '2_4':
                $this->addAttendPriv();
                $this->execSQL($this->getUpgradeFile('2.4'));
            case '2_5':
                $this->execSQL($this->getUpgradeFile('2.5'));
            case '2_6':
                $this->execSQL($this->getUpgradeFile('2.6'));
            case '2_7':
                $this->processBlockType();
                $this->execSQL($this->getUpgradeFile('2.7'));
            case '3_0':
                $this->execSQL($this->getUpgradeFile('3.0'));
            case '3_1':
                $this->processStatusForContact();
                $this->execSQL($this->getUpgradeFile('3.1'));
            case '3_2':
                $this->execSQL($this->getUpgradeFile('3.2'));
            case '3_2_1':
                $this->execSQL($this->getUpgradeFile('3.2.1'));
            case '3_3':
                $this->execSQL($this->getUpgradeFile('3.3'));
            case '3_4':
                $this->execSQL($this->getUpgradeFile('3.4'));
                $this->updateTradeCategories();
            case '3_5':
                $this->execSQL($this->getUpgradeFile('3.5'));
                $this->setSystemCategories();
                $this->setSalesAdminPrivileges();
            case '3_6':
                $this->execSQL($this->getUpgradeFile('3.6'));
            case '3_7':
                $this->execSQL($this->getUpgradeFile('3.7'));
                $this->updateDocPrivileges();
                $this->moveDocContent();
                $this->addProjectDoc();
            case '4_0': $this->addProjPrivilege(); 
            case '4_1': $this->execSQL($this->getUpgradeFile('4.1'));
                $this->updateMakeupActions();
            case '4_2': $this->execSQL($this->getUpgradeFile('4.2'));
            case '4_2_1': $this->execSQL($this->getUpgradeFile('4.2.1'));
            case '4_2_2':
            case '4_2_3':
            case '4_3_beta': $this->execSQL($this->getUpgradeFile('4.3.beta'));
            case '4_4': $this->processContractAddress();
                $this->execSQL($this->getUpgradeFile('4.4'));
            default: if(!$this->isError()) $this->loadModel('setting')->updateVersion($this->config->version);
        }

        $this->deletePatch();
    }

    /**
     * Create the confirm contents.
     * 
     * @param  string $fromVersion 
     * @access public
     * @return string
     */
    public function getConfirm($fromVersion)
    {
        $confirmContent = '';
        switch($fromVersion)
        {
            case '1_0_beta': $confirmContent .= file_get_contents($this->getUpgradeFile('1.0.beta'));
            case '1_1_beta': $confirmContent .= file_get_contents($this->getUpgradeFile('1.1.beta'));
            case '1_2_beta': $confirmContent .= file_get_contents($this->getUpgradeFile('1.2.beta'));
            case '1_3_beta': $confirmContent .= file_get_contents($this->getUpgradeFile('1.3.beta'));
            case '1_4_beta': $confirmContent .= file_get_contents($this->getUpgradeFile('1.4.beta'));
            case '1_5_beta': $confirmContent .= file_get_contents($this->getUpgradeFile('1.5.beta'));
            case '1_6'     : $confirmContent .= file_get_contents($this->getUpgradeFile('1.6'));
            case '2_0'     : $confirmContent .= file_get_contents($this->getUpgradeFile('2.0'));
            case '2_1'     : $confirmContent .= file_get_contents($this->getUpgradeFile('2.1'));
            case '2_3'     : $confirmContent .= file_get_contents($this->getUpgradeFile('2.3'));
            case '2_4'     : $confirmContent .= file_get_contents($this->getUpgradeFile('2.4'));
            case '2_5'     : $confirmContent .= file_get_contents($this->getUpgradeFile('2.5'));
            case '2_6'     : $confirmContent .= file_get_contents($this->getUpgradeFile('2.6'));
            case '2_7'     : $confirmContent .= file_get_contents($this->getUpgradeFile('2.7'));
            case '3_0'     : $confirmContent .= file_get_contents($this->getUpgradeFile('3.0'));
            case '3_1'     : $confirmContent .= file_get_contents($this->getUpgradeFile('3.1'));
            case '3_2'     : $confirmContent .= file_get_contents($this->getUpgradeFile('3.2'));
            case '3_2_1'   : $confirmContent .= file_get_contents($this->getUpgradeFile('3.2.1'));
            case '3_3'     : $confirmContent .= file_get_contents($this->getUpgradeFile('3.3'));
            case '3_4'     : $confirmContent .= file_get_contents($this->getUpgradeFile('3.4'));
            case '3_5'     : $confirmContent .= file_get_contents($this->getUpgradeFile('3.5'));
            case '3_6'     : $confirmContent .= file_get_contents($this->getUpgradeFile('3.6'));
            case '3_7'     : $confirmContent .= file_get_contents($this->getUpgradeFile('3.7'));
            case '4_1'     : $confirmContent .= file_get_contents($this->getUpgradeFile('4.1'));
            case '4_2'     : $confirmContent .= file_get_contents($this->getUpgradeFile('4.2'));
            case '4_2_1'   : $confirmContent .= file_get_contents($this->getUpgradeFile('4.2.1'));
            case '4_2_2'   :
            case '4_2_3'   :
            case '4_3_beta': $confirmContent .= file_get_contents($this->getUpgradeFile('4.3.beta'));
            case '4_4'     : $confirmContent .= file_get_contents($this->getUpgradeFile('4.4'));
        }
        return $confirmContent;
    }

    /**
     * Delete the patch record.
     * 
     * @access public
     * @return void
     */
    public function deletePatch()
    {
        return true;
        $this->dao->delete()->from(TABLE_EXTENSION)->where('type')->eq('patch')->exec();
    }

    /**
     * Get the upgrade sql file.
     * 
     * @param  string $version 
     * @access public
     * @return string
     */
    public function getUpgradeFile($version)
    {
        return $this->app->getBasepath() . 'db' . DS . 'upgrade' . $version . '.sql';
    }

    /**
     * Execute a sql.
     * 
     * @param  string  $sqlFile 
     * @access public
     * @return void
     */
    public function execSQL($sqlFile)
    {
        $mysqlVersion = $this->loadModel('install')->getMysqlVersion();
        $ignoreCode   = '|1050|1060|1062|1091|1169|';

        /* Read the sql file to lines, remove the comment lines, then join theme by ';'. */
        $sqls = explode("\n", file_get_contents($sqlFile));
        foreach($sqls as $key => $line) 
        {
            $line       = trim($line);
            $sqls[$key] = $line;
            if(strpos($line, '--') !== false or empty($line)) unset($sqls[$key]);
        }
        $sqls = explode(';', join("\n", $sqls));

        foreach($sqls as $sql)
        {
            $sql = trim($sql);
            if(empty($sql)) continue;

            if($mysqlVersion <= 4.1)
            {
                $sql = str_replace('DEFAULT CHARSET=utf8', '', $sql);
                $sql = str_replace('CHARACTER SET utf8 COLLATE utf8_general_ci', '', $sql);
            }

            /* Add table prefix. */
            if($this->config->db->prefix) $sql = preg_replace('/`(\w+)_/', "`{$this->config->db->prefix}\${1}_", $sql);

            try
            {
                $this->dbh->exec($sql);
            }
            catch (PDOException $e) 
            {
                $errorInfo = $e->errorInfo;
                $errorCode = $errorInfo[1];
                if(strpos($ignoreCode, "|$errorCode|") === false) self::$errors[] = $e->getMessage() . "<p>The sql is: $sql</p>";
            }
        }
    }

    /**
     * Judge any error occers.
     * 
     * @access public
     * @return bool
     */
    public function isError()
    {
        return !empty(self::$errors);
    }

    /**
     * Get errors during the upgrading.
     * 
     * @access public
     * @return array
     */
    public function getError()
    {
        $errors = self::$errors;
        self::$errors = array();
        return $errors;
    }

    /**
     * create cash entry.
     * 
     * @access public
     * @return bool
     */
    public function createCashEntry()
    {
        $entry = new stdclass();

        $entry->name     = 'cash';
        $entry->code     = 'cash';
        $entry->open     = 'iframe';
        $entry->order    = 2;
        $entry->ip       = '*';
        $entry->key      = '438d85f2c2b04372662c63ebfb1c4c2f';
        $entry->logo     = $this->config->webRoot . 'theme/default/images/ips/app-cash.png';
        $entry->login    = '../cash';
        $entry->ip       = '*';
        $entry->control  = 'simple';
        $entry->visible  = 1;
        $entry->size     = 'max';
        $entry->position = 'default';

        $block = REQUESTTYPE == 'GET' ? 'cash/index.php?m=block&f=index' : 'cash/block-index.html';
        $entry->block = $this->config->webRoot . $block;

        $this->dao->insert(TABLE_ENTRY)->data($entry)->exec();

        return !dao::isError();
    }

    /**
     * create team entry.
     * 
     * @access public
     * @return bool
     */
    public function createTeamEntry()
    {
        $entry = new stdclass();

        $entry->name     = 'team';
        $entry->code     = 'team';
        $entry->open     = 'iframe';
        $entry->order    = 4;
        $entry->ip       = '*';
        $entry->key      = '6c46d9fe76a1afa1cd61f946f1072d1e';
        $entry->logo     = $this->config->webRoot . 'theme/default/images/ips/app-team.png';
        $entry->login    = '../team';
        $entry->ip       = '*';
        $entry->control  = 'simple';
        $entry->size     = 'max';
        $entry->position = 'default';

        $block = REQUESTTYPE == 'GET' ? 'team/index.php?m=block&f=index' : 'team/block-index.html';
        $entry->block = $this->config->webRoot . $block;

        $this->dao->insert(TABLE_ENTRY)->data($entry)->exec();

        return !dao::isError();
    }

    /**
     * Transform block from config to block table.
     * 
     * @access public
     * @return bool
     */
    public function transformBlock()
    {
        $blocks  = $this->dao->select('*')->from(TABLE_CONFIG)->where('section')->eq('block')->andWhere('module')->eq('index')->fetchAll('id');
        $entries = $this->dao->select('id,code')->from(TABLE_ENTRY)->fetchPairs('id', 'code');

        foreach($blocks as $block)
        {
            if(empty($block->owner)) continue;
            $block->value = json_decode($block->value);

            $source  = '';
            $blockID = $block->value->type;
            if($block->value->type == 'system')
            {
                if($block->app == 'sys' and isset($block->value->entryID) and !isset($entries[$block->value->entryID])) continue;
                $source  = $block->app == 'sys' ? $entries[$block->value->entryID] : $block->app;
                $blockID = $block->value->blockID;
            }

            if($blockID == 'html') $block->value->params = helper::jsonEncode(array('html' => $block->value->html));
            if(!isset($block->value->params)) $block->value->params = array();

            $data = new stdclass();
            $data->account = $block->owner;
            $data->app     = $block->app;
            $data->title   = $block->value->name;
            $data->source  = $source;
            $data->block   = $blockID;
            $data->params  = helper::jsonEncode($block->value->params);
            $data->order   = str_replace('b', '', $block->key);
            $data->grid    = 3;

            $this->dao->replace(TABLE_BLOCK)->data($data)->exec();
        }
        if(dao::isError()) return false;

        $this->dao->delete()->from(TABLE_CONFIG)->where('section')->eq('block')->andWhere('module')->eq('index')->exec();
        return true;
    }

    /**
     * Change buildin entry name.
     * 
     * @access public
     * @return bool
     */
    public function changeBuildinName()
    {
        $this->app->loadLang('install', 'sys');

        foreach($this->lang->install->buildinEntry as $code => $name)
        {
            $this->dao->update(TABLE_ENTRY)
                ->set('name')->eq($name['name'])
                ->set('abbr')->eq($name['abbr'])
                ->set('buildin')->eq(1)
                ->set('integration')->eq(1)
                ->set('visible')->eq(1)
                ->where('code')->eq($code)
                ->exec();
        }

        if(dao::isError()) return false;
        return true;
    }

    /**
     * Compute contacteddate and contactedby fields.
     * 
     * @access public
     * @return bool
     */
    public function computeContactInfo()
    {
        $orders    = $this->dao->select('id')->from(TABLE_ORDER)->fetchAll();
        $customers = $this->dao->select('id')->from(TABLE_CUSTOMER)->fetchAll();
        $contracts = $this->dao->select('id')->from(TABLE_CONTRACT)->fetchAll();
        $contacts  = $this->dao->select('id')->from(TABLE_CONTACT)->fetchAll();

        foreach($orders as $order)
        {
            $lastContact = $this->dao->select('actor as contactedBy, date as contactedDate')->from(TABLE_ACTION)
                ->where('action')->eq('record')
                ->andWhere('objectType')->eq('order')
                ->andWhere('objectID')->eq($order->id)
                ->orderBY('date_desc')
                ->limit(1)
                ->fetch();
            if($lastContact) $this->dao->update(TABLE_ORDER)->data($lastContact)->where('id')->eq($order->id)->exec();
        }

        foreach($customers as $customer)
        {
            $lastContact = $this->dao->select('actor as contactedBy, date as contactedDate')->from(TABLE_ACTION)
                ->where('action')->eq('record')
                ->andWhere('customer')->eq($customer->id)
                ->orderBY('date_desc')
                ->limit(1)
                ->fetch();
            if($lastContact) $this->dao->update(TABLE_CUSTOMER)->data($lastContact)->where('id')->eq($customer->id)->exec();
        }

        foreach($contacts as $contact)
        {
            $lastContact = $this->dao->select('actor as contactedBy, date as contactedDate')->from(TABLE_ACTION)
                ->where('action')->eq('record')
                ->andWhere('contact')->eq($contact->id)
                ->orderBY('date_desc')
                ->limit(1)
                ->fetch();
            if($lastContact) $this->dao->update(TABLE_CONTACT)->data($lastContact)->where('id')->eq($contact->id)->exec();
        }

        foreach($contracts as $contract)
        {
            $lastContact = $this->dao->select('actor as contactedBy, date as contactedDate')->from(TABLE_ACTION)
                ->where('action')->eq('record')
                ->andWhere('objectType')->eq('contract')
                ->andWhere('objectID')->eq($contract->id)
                ->orderBY('date_desc')
                ->limit(1)
                ->fetch();
            if($lastContact) $this->dao->update(TABLE_CONTRACT)->data($lastContact)->where('id')->eq($contract->id)->exec();
        }

        return !dao::isError();

    }

    /**
     * Set content of company when upgrade from 1.3.beta.
     * 
     * @access public
     * @return bool
     */
    public function setCompanyContent()
    {
        if(empty($this->config->company->content) and $this->config->company->desc)
        {
            $this->dao->update(TABLE_CONFIG)->set('value')->eq($this->config->company->desc)->where('`key`')->eq('content')->andWhere('section')->eq('company')->exec();
            $this->dao->delete()->from(TABLE_CONFIG)->where('`key`')->eq('desc')->andWhere('section')->eq('company')->exec();
        }
        return !dao::isError();
    }

    /**
     * Set name of contract when upgrade from 1.4.beta.
     * 
     * @access public
     * @return bool
     */
    public function upgradeContractName()
    {
        $contracts = $this->dao->select('*')->from(TABLE_CONTRACT)->fetchAll();

        foreach($contracts as $contract)
        {
            $name = preg_replace('/^\[(\d+)\]/', '', $contract->name);
            $this->dao->update(TABLE_CONTRACT)->set('name')->eq($name)->where('id')->eq($contract->id)->exec();
        }

        return !dao::isError();
    }

    /**
     * Update project member.
     * 
     * @access public
     * @return bool
     */
    public function upgradeProjectMember()
    {
        $projects = $this->dao->select('*')->from(TABLE_PROJECT)->fetchAll('id');
        foreach($projects as $project)
        {
            $member = new stdclass();
            $member->type = 'project';
            $member->id   = $project->id;
 
            /* Move master to team table. */
            if(!empty($project->master))
            {
                $member->account = $project->master;
                $member->role    = 'role';
                $this->dao->replace(TABLE_TEAM)->data($member)->exec();
            }

            /* Move members to team table. */
            if(!empty($project->member))
            {
                $members = explode(',', $project->member);
                $member->role = 'member';
                foreach($members as $account)
                {
                    if($account == $project->master) continue;
                    if(!validater::checkAccount($account)) continue;

                    $member->account = $account;
                    $this->dao->replace(TABLE_TEAM)->data($member)->exec();
                }
            }

            return true;
        }
    }

    /**
     * Change system application logo path to relative path.
     * 
     * @access public
     * @return bool
     */
    public function upgradeEntryLogo()
    {
        $entryList = array('crm', 'cash', 'oa', 'team');
        foreach($entryList as $entry)
        {
            $entryObj = $this->dao->select('*')->from(TABLE_ENTRY)->where('code')->eq($entry)->fetch();
            $path     = substr($entryObj->logo, strpos($entryObj->logo, 'theme'));
            $this->dao->update(TABLE_ENTRY)->set('logo')->eq($path)->where('code')->eq($entry)->exec();
        }
        return !dao::isError();
    }

    /**
     * Update return records.
     * 
     * @access public
     * @return bool
     */
    public function upgradeReturnRecords()
    {
        $contracts = $this->dao->select('*')->from(TABLE_CONTRACT)->where('`return`')->eq('done')->fetchAll();
        if(empty($contracts)) return false;

        foreach($contracts as $contract)
        {
            $data = new stdclass();
            $data->contract     = $contract->id;
            $data->amount       = $contract->amount;
            $data->returnedBy   = $contract->returnedBy;
            $data->returnedDate = $contract->returnedDate;

            $this->dao->insert(TABLE_PLAN)->data($data)->autoCheck()->exec();
        }

        return !dao::isError();
    }

    /**
     * Update delivery records.
     * 
     * @access public
     * @return bool
     */
    public function upgradeDeliveryRecords()
    {
        $contracts = $this->dao->select('*')->from(TABLE_CONTRACT)->where('`delivery`')->eq('done')->fetchAll();
        if(empty($contracts)) return false;

        foreach($contracts as $contract)
        {
            $data = new stdclass();
            $data->contract      = $contract->id;
            $data->deliveredBy   = $contract->deliveredBy;
            $data->deliveredDate = $contract->deliveredDate;

            $this->dao->insert(TABLE_DELIVERY)->data($data)->autoCheck()->exec();
        }

        return !dao::isError();
    }

    /**
     * Add search priv when upgrade 1.5.beta.
     * 
     * @access public
     * @return bool
     */
    public function addSearchPriv()
    {
        $groups = $this->dao->select('id')->from(TABLE_GROUP)->fetchAll('id');
        foreach($groups as $group)
        {
            $priv = new stdclass();
            $priv->group  = $group->id;
            $priv->module = 'search';
            $priv->method = 'buildForm';
            $this->dao->replace('`sys_groupPriv`')->data($priv)->exec();

            $priv->method = 'buildQuery';
            $this->dao->replace('`sys_groupPriv`')->data($priv)->exec();

            $priv->method = 'saveQuery';
            $this->dao->replace('`sys_groupPriv`')->data($priv)->exec();

            $priv->method = 'deleteQuery';
            $this->dao->replace('`sys_groupPriv`')->data($priv)->exec();
        }

        return !dao::isError();
    }

    /**
     * Safe drop columns.
     * 
     * @param string $table 
     * @param string $columns 
     * @access public
     * @return bool
     */
    public function safeDropColumns($table, $columns)
    {
        if($columns == '') return false;

        $fieldsOBJ = $this->dao->query('desc ' . TABLE_PROJECT);
        while($field = $fieldsOBJ->fetch())
        {
            $fields[$field->Field] = $field->Field;
        }

        $columns = explode(',', $columns);
        foreach($columns as $column)
        {
            if($column == '') continue;
            if(isset($fields[$column]))
            {
                $this->dao->query("ALTER TABLE $table DROP $column;");
            }
        }

        return true;
    }

    /**
     * Add app priv when upgrade from 1.6.
     * 
     * @access public
     * @return bool
     */
    public function addPrivs()
    {
        $groups = $this->dao->select('id')->from(TABLE_GROUP)->fetchAll('id');

        foreach($groups as $group)
        {
            if($group->id == 1)
            {
                $privs = array('balance', 'depositor', 'order', 'product', 'project', 'schema', 'setting', 'task', 'trade');

                $modules['balance']   = array('browse', 'create', 'delete', 'edit');
                $modules['depositor'] = array('activate', 'browse', 'check', 'create', 'delete', 'edit', 'forbid', 'savebalance');
                $modules['order']     = array('delete');
                $modules['product']   = array('view');
                $modules['project']   = array('activate', 'suspend');
                $modules['schema']    = array('browse', 'create', 'delete', 'edit', 'view');
                $modules['setting']   = array('lang', 'reset');
                $modules['task']      = array('kanban', 'outline', 'start');
                $modules['trade']     = array('batchCreate', 'batchEdit', 'browse', 'create', 'delete', 'detail', 'edit', 'import', 'showimport', 'transfer');

                foreach($privs as $module)
                {
                    $priv = new stdclass();
                    $priv->group  = 1;
                    $priv->module = $module;

                    foreach($modules[$module] as $method)
                    {
                        $priv->method = $method;
                        $this->dao->replace('`sys_groupPriv`')->data($priv)->exec();
                    }
                }
            }

            if($group->id == 2)
            {
                $priv = new stdclass();
                $priv->group  = 2;
                $priv->module = 'depositor';
                $priv->method = 'delete';
                $this->dao->replace('`sys_groupPriv`')->data($priv)->exec();

                $priv->method = 'savabalance';
                $this->dao->replace('`sys_groupPriv`')->data($priv)->exec();
            }

            if($group->id == 3)
            {
                $priv = new stdclass();
                $priv->group  = 3;
                $priv->module = 'project';

                $methods = array('activate', 'finish', 'index', 'suspend');
                foreach($methods as $method)
                {
                    $priv->method = $method;
                    $this->dao->replace('`sys_groupPriv`')->data($priv)->exec();
                }
            }

            $priv = new stdclass();
            $priv->group  = $group->id;
            $priv->module = 'apppriv';
            $priv->method = 'crm';
            $this->dao->replace('`sys_groupPriv`')->data($priv)->exec();

            $priv->method = 'cash';
            $this->dao->replace('`sys_groupPriv`')->data($priv)->exec();

            $priv->method = 'oa';
            $this->dao->replace('`sys_groupPriv`')->data($priv)->exec();

            $priv->method = 'team';
            $this->dao->replace('`sys_groupPriv`')->data($priv)->exec();
        }

        return !dao::isError();
    }

    /**
     * To lower table.
     * 
     * @access public
     * @return bool
     */
    public function toLowerTable()
    {
        $results    = $this->dbh->query("show Variables like '%table_names'")->fetchAll();
        $hasLowered = false;
        foreach($results as $result)
        {
            if(strtolower($result->Variable_name) == 'lower_case_table_names' and $result->Value == 1)
            {
                $hasLowered = true;
                break;
            }
        }
        if($hasLowered) return true;

        $tables2Rename = $this->config->upgrade->lowerTables;
        if(!isset($tables2Rename)) return false;

        $tablesExists = $this->dbh->query('SHOW TABLES')->fetchAll();
        foreach($tablesExists as $key => $table) $tablesExists[$key] = current((array)$table);
        $tablesExists = array_flip($tablesExists);

        foreach($tables2Rename as $oldTable => $newTable)
        {
            if(!isset($tablesExists[$oldTable])) continue;
            
            $upgradebak = $newTable . '_othertablebak';
            if(isset($tablesExists[$upgradebak])) $this->dbh->query("DROP TABLE `$upgradebak`");
            if(isset($tablesExists[$newTable])) $this->dbh->query("RENAME TABLE `$newTable` TO `$upgradebak`");

            $tempTable = $oldTable . '_ranzhitmp';
            $this->dbh->query("RENAME TABLE `$oldTable` TO `$tempTable`");
            $this->dbh->query("RENAME TABLE `$tempTable` TO `$newTable`");
        }

        return true;
    }

    /**
     * Update app orders.
     * 
     * @access public
     * @return bool
     */
    public function updateAppOrder()
    {
        $entries = $this->dao->select('*')->from(TABLE_ENTRY)->orderBy('`order`, id')->fetchAll();
        $order   = 10;
        foreach($entries as $entry)
        {
            $this->dao->update(TABLE_ENTRY)->set('`order`')->eq($order)->where('id')->eq($entry->id)->exec();
            $order += 10;
        }
        return !dao::isError();
    }

    /**
     * Set assignedTo is closed if the task is closed when upgrade from 2.0.
     * 
     * @access public
     * @return bool
     */
    public function fixClosedTask()
    {
        $this->dao->update(TABLE_TASK)->set('assignedTo')->eq('closed')->where('status')->eq('closed')->exec();

        return !dao::isError();
    }

    /**
     * Set default salesGroup when upgrade from 2.0.
     * 
     * @access public
     * @return bool
     */
    public function setSalesGroup()
    {
        $sales = $this->dao->select('DISTINCT createdBy')->from(TABLE_CUSTOMER)->fetchPairs();

        $manageAllUsers = $this->dao->select('t1.account, t1.group, t2.group, t2.method')
            ->from(TABLE_USERGROUP)->alias('t1')
            ->leftJoin(TABLE_GROUPPRIV)->alias('t2')->on('t1.group=t2.group')
            ->where('t2.method')->eq('manageAll')
            ->fetchAll();

        if(!empty($manageAllUsers))
        {
            foreach($manageAllUsers as $manageAllUser)
            {
                if(isset($sales[$manageAllUser->account])) continue;
                $sales[$manageAllUser->account] = $manageAllUser->account;
            }
        }

        $users = ',' . implode(',', $sales) . ',';

        $group = new stdclass(); 
        $group->name  = '销售人员';
        $group->desc  = '';
        $group->users = $users;

        $this->dao->insert(TABLE_SALESGROUP)->data($group)->exec();

        $groupID = $this->dao->lastInsertID();

        if(!empty($manageAllUsers))
        {
            foreach($manageAllUsers as $manageAllUser)
            {
                $data['salesgroup'] = $groupID;
                $data['account']    = $manageAllUser->account;
                $data['priv']       = 'view';
                $this->dao->insert(TABLE_SALESPRIV)->data($data)->exec();

                $data['priv'] = 'edit';
                $this->dao->insert(TABLE_SALESPRIV)->data($data)->exec();
            }
        }

        $this->dao->delete()->from(TABLE_GROUPPRIV)->where('method')->eq('manageAll')->exec();

        return !dao::isError();
    }

    /**
     * Format product for order when upgrade from 2.0.
     * 
     * @access public
     * @return bool
     */
    public function fixOrderProduct()
    {
        $orders = $this->dao->select('*')->from(TABLE_ORDER)->fetchAll();

        foreach($orders as $order) 
        {
            $this->dao->update(TABLE_ORDER)->set('product')->eq(',' . $order->product . ',')->where('id')->eq($order->id)->exec();
        }

        return !dao::isError();
    }

    /**
     * Process desc of trade when upgrade from 2.2.
     * 
     * @access public
     * @return bool
     */
    public function processTradeDesc()
    {
        $trades = $this->dao->select('id, `desc`')->from(TABLE_TRADE)->fetchPairs();

        foreach($trades as $id => $trade)
        {
            $desc = strip_tags(htmlspecialchars_decode($trade));
            $this->dao->update(TABLE_TRADE)->set('desc')->eq($desc)->where('id')->eq($id)->exec();
        }

        return !dao::isError();
    }

    /**
     * Process customer edited date when upgrade from 2.3.
     * 
     * @access public
     * @return bool
     */
    public function processCustomerEditedDate()
    {
        $customers = $this->dao->select('*')->from(TABLE_CUSTOMER)->fetchAll('id');
        foreach($customers as $customer)
        {
            $editedDate = $customer->editedDate;
            $this->app->loadLang('order', 'crm');
            $orders = $this->dao->select('*')->from(TABLE_ORDER)->where('customer')->eq($customer->id)->fetchAll('id');
            foreach($orders as $order) 
            {
                if(!empty($order) and strtotime($order->editedDate) > strtotime($editedDate))  $editedDate = $order->editedDate;
                if(!empty($order) and strtotime($order->createdDate) > strtotime($editedDate)) $editedDate = $order->createdDate;
            }

            $contracts = $this->dao->select('*')->from(TABLE_CONTRACT)->where('customer')->eq($customer->id)->fetchAll('id');
            foreach($contracts as $contract) 
            {
                if(!empty($contract) and strtotime($contract->editedDate) > strtotime($editedDate))  $editedDate = $contract->editedDate;
                if(!empty($contract) and strtotime($contract->createdDate) > strtotime($editedDate)) $editedDate = $contract->createdDate;
            }

            $this->app->loadLang('contact', 'crm');
            $this->app->loadModuleConfig('contact', 'crm');
            $contacts = $this->dao->select('*')->from(TABLE_CONTACT)->where('customer')->eq($customer->id)->fetchAll('id');
            foreach($contacts as $contact) 
            {
                if(!empty($contact) and strtotime($contact->editedDate) > strtotime($editedDate))  $editedDate = $contact->editedDate;
                if(!empty($contact) and strtotime($contact->createdDate) > strtotime($editedDate)) $editedDate = $contact->createdDate;
            }

            if($editedDate != $customer->editedDate) $this->dao->update(TABLE_CUSTOMER)->set('editedDate')->eq($editedDate)->where('id')->eq($customer->id)->exec();
        }
        return !dao::isError();
    }

    /**
     * Add attend holiday leave trip privilages. when upgrade from 2.4.
     * 
     * @access public
     * @return bool
     */
    public function addAttendPriv()
    {
        $groups = $this->dao->select('id')->from(TABLE_GROUP)->fetchAll('id');
        $privs = array();
        $privs['attend']['personal'] = 'personal';
        $privs['attend']['edit']     = 'edit';
        $privs['leave']['personal']  = 'personal';
        $privs['leave']['create']    = 'create';
        $privs['leave']['edit']      = 'edit';
        $privs['leave']['delete']    = 'delete';
        $privs['trip']['personal']   = 'personal';
        $privs['trip']['create']     = 'create';
        $privs['trip']['edit']       = 'edit';
        $privs['trip']['delete']     = 'delete';
        foreach($groups as $group)
        {
            $priv = new stdclass();
            $priv->group  = $group->id;
            foreach($privs as $module => $modulePriv)
            {
                $priv->module = $module;
                foreach($modulePriv as $method => $methodPriv)
                {
                    $priv->method = $method;
                    $this->dao->replace(TABLE_GROUPPRIV)->data($priv)->exec();
                }
            }
        }

        return !dao::isError();
    }

    /**
     * Process block type.
     * 
     * @access public
     * @return bool
     */
    public function processBlockType()
    {
        $blocksHasType = 'order,contract,customer,task,project,thread';
        $blocks = $this->dao->select('*')->from(TABLE_BLOCK)->where('block')->in($blocksHasType)->fetchAll();
        foreach($blocks as $block)
        {
            $block->params = json_decode($block->params);
            if($block->block == 'project')
            {
                if(!isset($block->params->status))
                {
                    $block->params->status = 'doing';
                    $params = helper::jsonEncode($block->params);
                    $this->dao->update(TABLE_BLOCK)->set('params')->eq($params)->where('id')->eq($block->id)->exec();
                }
            }
            else
            {
                if(!isset($block->params->type))
                {
                    if($block->block == 'order')    $block->params->type = 'assignedTo';
                    if($block->block == 'contract') $block->params->type = 'returnedBy';
                    if($block->block == 'customer') $block->params->type = 'today';
                    if($block->block == 'task')     $block->params->type = 'assignedTo';
                    if($block->block == 'thread')   $block->params->type = 'new';

                    $params = helper::jsonEncode($block->params);
                    $this->dao->update(TABLE_BLOCK)->set('params')->eq($params)->where('id')->eq($block->id)->exec();
                }
            }
        }

        return !dao::isError();
    }

    /**
     * Remove old todo module files. 
     * 
     * @access public
     * @return bool
     */
    public function removeOldTodoFile()
    {
        $dir = $this->app->getBasePath() . "app/oa/todo/";
        if(!file_exists($dir)) return true;
        return $this->app->loadClass('zfile')->removeDir($dir);
    }

    /**
     * Process status for contact when upgrade from 3.1.
     * 
     * @access public
     * @return bool
     */
    public function processStatusForContact()
    {
        $contactList = $this->dao->select('*')->from(TABLE_CONTACT)->fetchAll('id');
        foreach($contactList as $id => $contact)
        {
            $this->dao->update(TABLE_CONTACT)->set('status')->eq('normal')->where('id')->eq($id)->exec();
        }

        return !dao::isError();
    }

    /**
     * Update trade categories.
     * 
     * @access public
     * @return bool
     */
    public function updateTradeCategories() 
    {
        $this->app->loadLang('tree', 'sys');

        $majorIncomeCategories = $this->dao->select('*')->from(TABLE_CATEGORY)
            ->where('major')->eq('1')
            ->andWhere('type')->eq('in')
            ->andWhere('grade')->eq('1')
            ->fetchAll();

        $majorExpenseCategories = $this->dao->select('*')->from(TABLE_CATEGORY)
            ->where('major')->eq('1')
            ->andWhere('type')->eq('out')
            ->andWhere('grade')->eq('1')
            ->fetchAll();

        $this->dao->update(TABLE_CATEGORY)->set('major')->eq(0)->where('type')->in('in,out')->andWhere('grade')->ne('1')->exec();

        foreach($this->lang->upgrade->majorList['3_5'] as $key => $major)
        {
            $data = new stdclass();
            $data->name  = $major;
            $data->major = $key;
            $data->type  = $key < 3 ? 'in' : 'out';
            $data->grade = '1';

            $this->dao->insert(TABLE_CATEGORY)->data($data)->exec();
            $newCategoryID = $this->dao->lastInsertID();
            $this->dao->update(TABLE_CATEGORY)->set('path')->eq(',' . $newCategoryID . ',')->where('id')->eq($newCategoryID)->exec();
            
            if($key == '1' or $key == '3')
            {
                $categories = $key == '1' ? $majorIncomeCategories : $majorExpenseCategories;
                foreach($categories as $category)
                {
                    $children = $this->dao->select('*')->from(TABLE_CATEGORY)->where('path')->like($category->path . '%')->fetchAll();
                    foreach($children as $child)
                    {
                        $path  = ',' . $newCategoryID . $child->path;
                        $grade = $child->grade + 1;
                        if($grade == 2) $this->dao->update(TABLE_CATEGORY)->set('major')->eq(0)->set('path')->eq($path)->set('grade')->eq($grade)->set('parent')->eq($newCategoryID)->where('id')->eq($child->id)->exec();
                        if($grade != 2) $this->dao->update(TABLE_CATEGORY)->set('major')->eq(0)->set('path')->eq($path)->set('grade')->eq($grade)->where('id')->eq($child->id)->exec();
                    }
                }
            }
        }

        return !dao::isError();
    }

    /**
     * Set system category.
     * 
     * @access public
     * @return bool
     */
    public function setSystemCategories()
    {
        $this->app->loadLang('tree', 'sys');
        foreach($this->lang->upgrade->majorList['3_6'] as $key => $major)
        {
            if($key < 5) continue;

            $data = new stdclass();
            $data->name  = $major;
            $data->major = $key;
            $data->type  = $key == 5 ? 'in' : 'out';
            $data->grade = '1';

            $this->dao->insert(TABLE_CATEGORY)->data($data)->exec();
            $newCategoryID = $this->dao->lastInsertID();
            $this->dao->update(TABLE_CATEGORY)->set('path')->eq(',' . $newCategoryID . ',')->where('id')->eq($newCategoryID)->exec();

            if($key == 5) $this->dao->update(TABLE_TRADE)->set('category')->eq($newCategoryID)->where('category')->eq('profit')->exec();
            if($key == 6) $this->dao->update(TABLE_TRADE)->set('category')->eq($newCategoryID)->where('category')->eq('loss')->exec();
            if($key == 7) $this->dao->update(TABLE_TRADE)->set('category')->eq($newCategoryID)->where('category')->eq('fee')->exec();
        }

        return !dao::isError();
    }

    /**
     * Set sales admin privileges.
     * 
     * @access public
     * @return bool
     */
    public function setSalesAdminPrivileges()
    {
        $groups = $this->dao->select('`group`')->from(TABLE_GROUPPRIV)->where('module')->eq('sales')->andWhere('method')->eq('browse')->fetchPairs();
        $grouppriv = new stdclass();
        $grouppriv->module = 'sales';
        $grouppriv->method = 'admin';
        foreach($groups as $group)
        {
            $grouppriv->group = $group;
            $this->dao->insert(TABLE_GROUPPRIV)->data($grouppriv)->exec();
        }
        return !dao::isError();
    }

    /**
     * Set doc entry privileges when upgrade from 3.7.
     * 
     * @access public
     * @return bool
     */
    public function updateDocPrivileges()
    {
        $groups = $this->dao->select('`group`')->from(TABLE_GROUPPRIV)->where('module')->eq('doc')->fetchPairs();
        foreach($groups as $group)
        {
            $data = new stdclass();
            $data->group = $group;
            $data->module = 'apppriv';
            $data->method = 'doc';
            $this->dao->replace(TABLE_GROUPPRIV)->data($data)->exec();

            $data->module = 'doc';
            $data->method = 'index';
            $this->dao->replace(TABLE_GROUPPRIV)->data($data)->exec();

            $data->method = 'allLibs';
            $this->dao->replace(TABLE_GROUPPRIV)->data($data)->exec();

            $data->method = 'showFiles';
            $this->dao->replace(TABLE_GROUPPRIV)->data($data)->exec();

            $data->method = 'projectLibs';
            $this->dao->replace(TABLE_GROUPPRIV)->data($data)->exec();

            $data->method = 'sort';
            $this->dao->replace(TABLE_GROUPPRIV)->data($data)->exec();
        }

        return !dao::isError();
    }

    /**
     * Move doc content to table oa_doccontent.
     * 
     * @access public
     * @return bool
     */
    public function moveDocContent()
    {
        $descDoc = $this->dao->query('DESC ' .  TABLE_DOC)->fetchAll();
        $processFields = 0;
        foreach($descDoc as $field)
        {
            if($field->Field == 'content' or $field->Field == 'digest' or $field->Field == 'url') $processFields ++; 
        }
        if($processFields < 3) return true;

        $this->dao->exec('TRUNCATE TABLE ' . TABLE_DOCCONTENT);
        $stmt = $this->dao->select('id,title,digest,content,url')->from(TABLE_DOC)->query();
        $fileGroups = $this->dao->select('id,objectID')->from(TABLE_FILE)->where('objectType')->eq('doc')->fetchGroup('objectID', 'id');
        while($doc = $stmt->fetch())
        {
            $url = empty($doc->url) ? '' : urldecode($doc->url);
            $docContent = new stdclass();
            $docContent->doc      = $doc->id;
            $docContent->title    = $doc->title;
            $docContent->digest   = $doc->digest;
            $docContent->content  = $doc->content;
            $docContent->content .= empty($url) ? '' : $url;
            $docContent->version  = 1;
            $docContent->type     = 'html';
            if(isset($fileGroups[$doc->id])) $docContent->files = join(',', array_keys($fileGroups[$doc->id]));
            $this->dao->insert(TABLE_DOCCONTENT)->data($docContent)->exec();
        }
        $this->dao->exec('ALTER TABLE ' . TABLE_DOC . ' DROP `digest`');
        $this->dao->exec('ALTER TABLE ' . TABLE_DOC . ' DROP `content`');
        $this->dao->exec('ALTER TABLE ' . TABLE_DOC . ' DROP `url`');
        return true;
    }

    /**
     * Add project default doc.
     * 
     * @access public
     * @return bool
     */
    public function addProjectDoc()
    {
        set_time_limit(0);
        $this->app->loadLang('doc', 'doc');

        $allProjectIdList  = $this->dao->select('id,name,whitelist')->from(TABLE_PROJECT)->where('deleted')->eq('0')->fetchAll('id');
        foreach($allProjectIdList as $projectID => $project)
        {
            $this->dao->delete()->from(TABLE_DOCLIB)->where('project')->eq($projectID)->exec();

            $lib = new stdclass();
            $lib->project = $projectID;
            $lib->name    = $this->lang->doc->projectMainLib;
            $lib->main    = 1;
            $lib->private = 0;
            $lib->createdDate = helper::now();

            $teams = $this->dao->select('account')->from(TABLE_TEAM)->where('type')->eq('project')->andWhere('id')->eq($projectID)->fetchPairs('account', 'account');
            $lib->users = join(',', $teams);
            $lib->groups = isset($project->whitelist) ? $project->whitelist : '';
            $this->dao->insert(TABLE_DOCLIB)->data($lib)->exec();
        }

        return !dao::isError();
    }

    /**
     * Add privilege of proj app when upgrade from 4.0.
     * 
     * @access public
     * @return bool
     */
    public function addProjPrivilege()
    {
        $groups = $this->dao->select('*')->from(TABLE_GROUP)->fetchAll();

        $data = new stdclass();
        $data->module = 'apppriv';
        $data->method = 'proj';

        foreach($groups as $group)
        {
            $data->group = $group->id;
            $this->dao->replace(TABLE_GROUPPRIV)->data($data)->exec();
        }

        return !dao::isError();
    }

    /**
     * Update makeup actions. 
     * 
     * @access public
     * @return bool
     */
    public function updateMakeupActions()
    {
        $makeupList = $this->loadModel('makeup', 'oa')->getList();
        foreach($makeupList as $makeup)
        {
            $this->dao->update(TABLE_ACTION)->set('objectType')->eq('makeup')->where('objectType')->eq('overtime')->andWhere('objectID')->eq($makeup->id)->exec();
        }

        return !dao::isError();
    }

    /**
     * Process addresses of contracts. 
     * 
     * @access public
     * @return bool
     */
    public function processContractAddress()
    {
        $address = new stdclass();
        $address->objectType = 'customer';
    
        $this->app->loadLang('contract', 'crm');
        $contracts = $this->dao->select('*')->from(TABLE_CONTRACT)->where('address')->ne('')->fetchAll();
        foreach($contracts as $contract)
        {
            $address->objectID = $contract->customer;
            $address->title    = $this->lang->contract->address;
            $address->location = $contract->address;

            $this->dao->insert(TABLE_ADDRESS)->data($address)->exec();
            $addressID = $this->dao->lastInsertId();

            $this->dao->update(TABLE_CONTRACT)->set('address')->eq($addressID)->where('id')->eq($contract->id)->exec();
        }
        return !dao::isError();
    }
}
