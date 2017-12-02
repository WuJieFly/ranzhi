<?php
/**
 * The control file of makeup of Ranzhi.
 *
 * @copyright   Copyright 2009-2017 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Gang Liu <liugang@cnezsoft.com> 
 * @package     makeup
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
class makeup extends control
{
    /**
     * index 
     * 
     * @access public
     * @return void
     */
    public function index()
    {
        $this->locate(inlink('personal'));
    }

    /**
     * Personal's makeup. 
     * 
     * @param  string  $date 
     * @param  string  $orderBy 
     * @access public
     * @return void
     */
    public function personal($date = '', $orderBy = 'id_desc')
    {
        die($this->fetch('makeup', 'browse', "type=personal&date=$date&orderBy=$orderBy", 'oa'));
    }

    /**
     * Department's makeup. 
     * 
     * @param  string   $date 
     * @param  string   $orderBy 
     * @access public
     * @return void
     */
    public function browseReview($date = '', $orderBy = 'id_desc')
    {
        die($this->fetch('makeup', 'browse', "type=browseReview&date=$date&orderBy=$orderBy", 'oa'));
    }

    /**
     * Company's makeup. 
     * 
     * @param  string   $date 
     * @param  string   $orderBy 
     * @access public
     * @return void
     */
    public function company($date = '', $orderBy = 'id_desc')
    {
        die($this->fetch('makeup', 'browse', "type=company&date=$date&orderBy=$orderBy", 'oa'));
    }

    /**
     * Browse makeup.
     * 
     * @param  string   $type 
     * @param  string   $date 
     * @param  string   $orderBy 
     * @access public
     * @return void
     */
    public function browse($type = 'personal', $date = '', $orderBy = 'id_desc')
    {
        /* If type is browseReview, display all makeups wait to review. */
        if($type == 'browseReview')
        {
            $date         = '';
            $currentYear  = ''; 
            $currentMonth = ''; 
        }
        else
        {
            if($date == '' or (strlen($date) != 6 and strlen($date) != 4)) $date = date("Ym");
            $currentYear  = substr($date, 0, 4);
            $currentMonth = strlen($date) == 6 ? substr($date, 4, 2) : '';
            $monthList    = $this->makeup->getAllMonth($type);
            $yearList     = array_keys($monthList);

            $this->view->currentYear  = $currentYear;
            $this->view->currentMonth = $currentMonth;
            $this->view->monthList    = $monthList;
            $this->view->yearList     = $yearList;
        }

        $makeupList  = array();
        $deptList    = $this->loadModel('tree')->getPairs(0, 'dept');
        $deptList[0] = '';

        if($type == 'personal')
        {
            $makeupList = $this->makeup->getList($type, $currentYear, $currentMonth, $this->app->user->account, '', '', $orderBy);
        }
        elseif($type == 'browseReview')
        {
            if($this->app->user->admin == 'super')
            {
                $makeupList = $this->makeup->getList($type, $currentYear, $currentMonth, '', '', '', $orderBy);
            }
            else
            {
                $reviewedBy = $this->makeup->getReviewedBy();
                if($reviewedBy)
                { 
                    if($reviewedBy == $this->app->user->account)
                    {
                        $makeupList = $this->makeup->getList($type, $currentYear, $currentMonth, '', array_keys($deptList), '', $orderBy);
                    }
                }
                else
                {
                    $depts = $this->loadModel('tree')->getDeptManagedByMe($this->app->user->account);
                    if(empty($depts))
                    {
                        $makeupList = array();
                    }
                    else
                    {
                        $makeupList = $this->makeup->getList($type, $currentYear, $currentMonth, '', array_keys($depts), '', $orderBy);
                    }
                }
            }
        }
        elseif($type == 'company')
        {
            $makeupList = $this->makeup->getList($type, $currentYear, $currentMonth, '', '', '', $orderBy);
        }

        $this->session->set('makeupList', $this->app->getURI(true));

        $this->view->title      = $this->lang->makeup->browse;
        $this->view->type       = $type;
        $this->view->deptList   = $deptList;
        $this->view->users      = $this->loadModel('user')->getPairs();
        $this->view->makeupList = $makeupList;
        $this->view->date       = $date;
        $this->view->orderBy    = $orderBy;
        $this->display();
    }

    /**
     * Review an makeup.
     * 
     * @param  int      $id 
     * @param  string   $status 
     * @access public
     * @return void
     */
    public function review($id, $status)
    {
        /* Check privilage. */
        $canReview = false;
        if($this->app->user->admin == 'super') 
        {
            $canReview = true;
        }
        else
        {
            $reviewedBy = $this->makeup->getReviewedBy();
            if($reviewedBy)
            { 
                if($reviewedBy == $this->app->user->account) $canReview = true;
            }
            else
            {
                $makeup      = $this->makeup->getById($id);
                $createdUser = $this->loadModel('user')->getByAccount($makeup->createdBy);
                $dept        = $this->loadModel('tree')->getById($createdUser->dept);
                if($dept && $this->app->user->account == trim($dept->moderators, ',')) $canReview = true;
            }
        }

        if($status == 'pass')
        {
            if(!$canReview) $this->send(array('result' => 'fail', 'message' => $this->lang->makeup->denied));

            $this->makeup->review($id, $status);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $actionID = $this->loadModel('action')->create('makeup', $id, 'reviewed', '', $this->lang->makeup->statusList[$status]);
            $this->sendmail($id, $actionID);

            $this->send(array('result' => 'success'));
        }
        if($status == 'reject')
        {
            if($_POST)
            {
                if(!$canReview) $this->send(array('result' => 'fail', 'message' => $this->lang->makeup->denied));

                if(!$this->post->comment) $this->send(array('result' => 'fail', 'message' => array('comment' => sprintf($this->lang->error->notempty, $this->lang->makeup->rejectReason))));

                $this->makeup->review($id, $status);
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

                $actionID = $this->loadModel('action')->create('makeup', $id, 'reviewed', $this->post->comment, $this->lang->makeup->statusList[$status]);
                $this->sendmail($id, $actionID);
    
                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
            }

            $this->view->title = $this->lang->makeup->review;
            $this->view->id    = $id;
            $this->display();
        }
    }

    /**
     * Batch review makeups. 
     * 
     * @param  string $status
     * @access public
     * @return void
     */
    public function batchReview($status = 'pass')
    {
        if(!$this->post->makeupIDList) $this->send(array('result' => 'fail', 'message' => $this->lang->makeup->nodata));

        /* Check privilage. */
        $canReview    = false;
        $makeupIDList = $this->post->makeupIDList;
        if($this->app->user->admin == 'super')
        {
            $canReview = true;
        }
        else
        {
            $reviewedBy = $this->makeup->getReviewedBy();
            if($reviewedBy)
            { 
                if($reviewedBy == $this->app->user->account) $canReview = true;
            }
            else
            {
                $makeupIDList = $this->dao->select('t1.id')->from(TABLE_OVERTIME)->alias('t1')
                    ->leftJoin(TABLE_USER)->alias('t2')->on('t1.createdBy=t2.account')
                    ->leftJoin(TABLE_CATEGORY)->alias('t3')->on('t2.dept=t3.id')
                    ->where('t1.id')->in($makeupIDList)
                    ->andWhere('t3.type')->eq('dept')
                    ->andWhere('t3.moderators')->eq(",{$this->app->user->account},")
                    ->fetchPairs();
                if($makeupIDList) $canReview = true;
            }
        }

        if($status == 'pass')
        {
            if(!$canReview) $this->send(array('result' => 'fail', 'message' => $this->lang->makeup->denied));

            foreach($makeupIDList as $id)
            {
                $this->makeup->review($id, $status);
                if(dao::isError()) continue;

                $actionID = $this->loadModel('action')->create('makeup', $id, 'reviewed', '', $this->lang->makeup->statusList[$status]);
                $this->sendmail($id, $actionID);
            }
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->send(array('result' => 'success', 'message' => $this->lang->makeup->reviewSuccess, 'locate' => 'reload'));
        }
    }

    /**
     * Create an makeup.
     * 
     * @param  string $date
     * @access public
     * @return void
     */
    public function create($date = '')
    {
        if($_POST)
        {
            $result = $this->makeup->create();
            if(is_array($result)) $this->send($result);

            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $makeupID = $result;
            $actionID = $this->loadModel('action')->create('makeup', $makeupID, 'created');
            $this->sendmail($makeupID, $actionID);
            
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('personal')));
        }

        if($date)
        {
            $date   = date('Y-m-d', strtotime($date));
            $makeup = $this->makeup->getByDate($date, $this->app->user->account);
            if($makeup && strpos(',wait,draft,', $makeup->status) !== false) $this->locate(inlink('edit', "id=$makeup->id"));
        }

        $leavePairs = array('');
        $leaveList  = $this->loadModel('leave', 'oa')->getList('company', '', '', $this->app->user->account, '', 'pass');
        foreach($leaveList as $key => $leave)
        {
            $leavePairs[$leave->id] = formatTime($leave->begin . ' ' . $leave->start, DT_DATETIME2) . ' ~ ' . formatTime($leave->end . ' ' . $leave->finish, DT_DATETIME2);
        }

        $this->view->title      = $this->lang->makeup->create;
        $this->view->leavePairs = $leavePairs;
        $this->view->date       = $date;
        $this->display();
    }

    /**
     * Edit makeup.
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function edit($id)
    {
        $makeup = $this->makeup->getById($id);
        /* check privilage. */
        if($makeup->createdBy != $this->app->user->account) 
        {
            $locate     = helper::safe64Encode(helper::createLink('oa.makeup', 'browse'));
            $noticeLink = helper::createLink('notice', 'index', "type=accessLimited&locate={$locate}");
            die(js::locate($noticeLink));
        }

        if($_POST)
        {
            $result = $this->makeup->update($id);
            if(is_array($result) && $result['result'] == 'fail') $this->send($result);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            if($result)
            {
                $actionID = $this->loadModel('action')->create('makeup', $id, 'edited');
                $this->action->logHistory($actionID, $result);
            }
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $leavePairs = array('');
        $leaveList  = $this->loadModel('leave', 'oa')->getList('company', '', '', $this->app->user->account, '', 'pass');
        foreach($leaveList as $key => $leave)
        {
            $leavePairs[$leave->id] = formatTime($leave->begin . ' ' . $leave->start, DT_DATETIME2) . ' ~ ' . formatTime($leave->end . ' ' . $leave->finish, DT_DATETIME2);
        }

        $this->view->title      = $this->lang->makeup->edit;
        $this->view->leavePairs = $leavePairs;
        $this->view->makeup     = $makeup;
        $this->display();
    }

    /**
     * View makeup.
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function view($id, $type = '')
    {
        $makeup = $this->makeup->getById($id);

        $leavePairs = array('');
        $leaveList  = $this->loadModel('leave', 'oa')->getByIdList(trim($makeup->leave, ','));
        foreach($leaveList as $key => $leave)
        {
            $leavePairs[$leave->id] = formatTime($leave->begin . ' ' . $leave->start, DT_DATETIME2) . ' ~ ' . formatTime($leave->end . ' ' . $leave->finish, DT_DATETIME2);
        }
        $this->view->title      = $this->lang->makeup->view;
        $this->view->makeup     = $makeup;
        $this->view->users      = $this->loadModel('user')->getPairs();
        $this->view->leavePairs = $leavePairs;
        $this->view->type       = $type;
        $this->display();
    }

    /**
     * Delete makeup.
     * 
     * @param  int     $id 
     * @access public
     * @return void
     */
    public function delete($id)
    {
        $makeup = $this->makeup->getById($id);
        if($makeup->createdBy != $this->app->user->account) $this->send(array('result' => 'fail', 'message' => $this->lang->makeup->denied));

        $this->makeup->delete($id);
        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
        $this->send(array('result' => 'success'));
    }

    /**
     * Switch status for a makeup.
     * 
     * @param  int    $makeupID 
     * @access public
     * @return void
     */
    public function switchStatus($makeupID)
    {
        $makeup = $this->makeup->getById($makeupID);
        if(!$makeup) return false;
        if($makeup->createdBy != $this->app->user->account) $this->send(array('result' => 'fail', 'message' => $this->lang->makeup->denied));

        $dates = range(strtotime($makeup->begin), strtotime($makeup->end), 60 * 60 * 24);
 
        if($makeup->status == 'wait')
        {
            $this->dao->update(TABLE_OVERTIME)->set('status')->eq('draft')->where('id')->eq($makeupID)->exec();

            $actionID = $this->loadModel('action')->create('makeup', $makeupID, 'revoked');
            $this->sendmail($makeupID, $actionID);

            $this->loadModel('attend', 'oa')->batchUpdate($dates, $makeup->createdBy);
        }

        if($makeup->status == 'draft')
        {
            $this->dao->update(TABLE_OVERTIME)->set('status')->eq('wait')->where('id')->eq($makeupID)->exec();

            $actionID = $this->loadModel('action')->create('makeup', $makeupID, 'commited');
            $this->sendmail($makeupID, $actionID);

            $this->loadModel('attend', 'oa')->batchUpdate($dates, $makeup->createdBy, '', 'makeup');
        }

        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
        $this->send(array('result' => 'success'));
    }

    /**
     * Send email.
     * 
     * @param  int    $makeupID 
     * @param  int    $actionID 
     * @access public
     * @return void
     */
    public function sendmail($makeupID, $actionID)
    {
        /* Reset $this->output. */
        $this->clear();

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Set toList and ccList. */
        $makeup = $this->makeup->getById($makeupID);
        $users  = $this->loadModel('user')->getPairs();
        $toList = '';
        if($action->action == 'reviewed')
        {
            $toList  = $makeup->createdBy;
            $subject = "{$this->lang->makeup->common}{$this->lang->makeup->statusList[$makeup->status]}#{$makeup->id} " . zget($users, $makeup->createdBy);
        }
        if($action->action == 'created' or $action->action == 'revoked' or $action->action == 'commited')
        {
            $reviewedBy = $this->makeup->getReviewedBy();
            if($reviewedBy)
            {
                $toList = $reviewedBy; 
            }
            else
            {
               $dept = $this->loadModel('tree')->getById($this->app->user->dept);
               if($dept) $toList = trim($dept->moderators, ',');
            }

            $subject = "{$this->lang->makeup->common}#{$makeup->id} " . zget($users, $makeup->createdBy);
        }

        /* send notice if user is online and return failed accounts. */
        $toList = $this->loadModel('action')->sendNotice($actionID, $toList);

        /* Create the email content. */
        $this->view->makeup = $makeup;
        $this->view->action = $action;
        $this->view->users  = $users;

        $mailContent = $this->parse($this->moduleName, 'sendmail');

        /* Send emails. */
        $this->loadModel('mail')->send($toList, $subject, $mailContent);
        if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
    }

    /**
     * get data to export.
     * 
     * @param  string $mode 
     * @param  string $orderBy 
     * @access public
     * @return void
     */
    public function export($mode = 'all', $orderBy = 'id_desc')
    { 
        if($_POST)
        {
            $deptList  = $this->loadModel('tree')->getPairs('', 'dept');
            $users     = $this->loadModel('user')->getList();
            $userPairs = array();
            $userDepts = array();
            foreach($users as $key => $user) 
            {
                $userPairs[$user->account] = $user->realname;
                $userDepts[$user->account] = zget($deptList, $user->dept, ' ');
            }

            /* Create field lists. */
            $fields = explode(',', $this->config->makeup->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($this->lang->makeup->$fieldName) ? $this->lang->makeup->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            $fields['dept'] = $this->lang->user->dept;

            $makeups = array();
            if($mode == 'all')
            {
                $makeupQueryCondition = $this->session->makeupQueryCondition;
                if(strpos($makeupQueryCondition, 'limit') !== false) $makeupQueryCondition = substr($makeupQueryCondition, 0, strpos($makeupQueryCondition, 'limit'));
                $stmt = $this->dbh->query($makeupQueryCondition);
                while($row = $stmt->fetch()) $makeups[$row->id] = $row;
            }
            if($mode == 'thisPage')
            {
                $stmt = $this->dbh->query($this->session->makeupQueryCondition);
                while($row = $stmt->fetch()) $makeups[$row->id] = $row;
            }

            foreach($makeups as $makeup)
            {
                $makeup->createdBy  = zget($userPairs, $makeup->createdBy);
                $makeup->dept       = zget($userDepts, $makeup->createdBy);
                $makeup->type       = zget($this->lang->makeup->typeList, $makeup->type);
                $makeup->begin      = $makeup->begin . ' ' . $makeup->start;
                $makeup->end        = $makeup->end   . ' ' . $makeup->finish;
                $makeup->desc       = htmlspecialchars_decode($makeup->desc);
                $makeup->desc       = str_replace("<br />", "\n", $makeup->desc);
                $makeup->desc       = str_replace('"', '""', $makeup->desc);
                $makeup->status     = zget($this->lang->makeup->statusList, $makeup->status);
                $makeup->reviewedBy = zget($userPairs, $makeup->reviewedBy);
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $makeups);
            $this->post->set('kind', 'makeup');
            $this->fetch('file', 'export2CSV' , $_POST);
        }

        $this->display();
    }

    /**
     * Set reviewer. 
     * 
     * @param  string $module 
     * @access public
     * @return void
     */
    public function setReviewer($module = '')
    {
        if($_POST)
        {
            $this->loadModel('setting')->setItem('system.oa.makeup..reviewedBy', $this->post->reviewedBy);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess));
        }

        if($module)
        {
            $this->lang->menuGroups->makeup = $module;
            $this->lang->makeup->menu       = $this->lang->$module->menu;
        }

        $this->view->title      = $this->lang->makeup->setReviewer;
        $this->view->users      = $this->loadModel('user', 'sys')->getPairs('noclosed,noforbidden,nodeleted');
        $this->view->reviewedBy = $this->makeup->getReviewedBy();
        $this->display();
    }
}
