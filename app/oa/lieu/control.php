<?php
/**
 * The control file of lieu of Ranzhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     lieu
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
class lieu extends control
{
    /**
     * Index.
     * 
     * @access public
     * @return void
     */
    public function index()
    {
        $this->locate(inlink('personal'));
    }

    /**
     * personal's lieu. 
     * 
     * @param  string $date 
     * @access public
     * @return void
     */
    public function personal($date = '', $orderBy = 'id_desc')
    {
        die($this->fetch('lieu', 'browse', "type=personal&date=$date&orderBy=$orderBy", 'oa'));
    }

    /**
     * The lieu browse for review. 
     * 
     * @param  string  $date 
     * @access public
     * @return void
     */
    public function browseReview($date = '', $orderBy = 'id_desc')
    {
        die($this->fetch('lieu', 'browse', "type=browseReview&date=$date&orderBy=$orderBy", 'oa'));
    }

    /**
     * Company's lieu. 
     * 
     * @param  string $date 
     * @access public
     * @return void
     */
    public function company($date = '', $orderBy = 'id_desc')
    {
        die($this->fetch('lieu', 'browse', "type=company&date=$date&orderBy=$orderBy", 'oa'));
    }

    /**
     * Browse lieu list.
     * 
     * @param  string $type 
     * @param  string $date 
     * @access public
     * @return void
     */
    public function browse($type = 'personal', $date = '', $orderBy = 'id_desc')
    {
        /* If type is browseReview, display all lieus wait to review. */
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
            $monthList    = $this->lieu->getAllMonth($type);
            $yearList     = array_keys($monthList);

            $this->view->currentYear  = $currentYear;
            $this->view->currentMonth = $currentMonth;
            $this->view->monthList    = $monthList;
            $this->view->yearList     = $yearList;
        }

        $lieuList    = array();
        $deptList    = $this->loadModel('tree')->getPairs(0, 'dept');
        $deptList[0] = '/';

        if($type == 'personal')
        {
            $lieuList = $this->lieu->getList($type, $currentYear, $currentMonth, $this->app->user->account, '', '', $orderBy);
        }
        elseif($type == 'browseReview')
        {
            if($this->app->user->admin == 'super')
            {
                $lieuList = $this->lieu->getList($type, $currentYear, $currentMonth, '', '', '', $orderBy);
            }
            else
            {
                $reviewedBy = $this->lieu->getReviewedBy();
                if($reviewedBy)
                { 
                    if($reviewedBy == $this->app->user->account)
                    {
                        $lieuList = $this->lieu->getList($type, $currentYear, $currentMonth, '', array_keys($deptList), '', $orderBy);
                    }
                }
                else
                {
                    $depts = $this->loadModel('tree')->getDeptManagedByMe($this->app->user->account);
                    if(empty($depts))
                    {
                        $lieuList = array();
                    }
                    else
                    {
                        $lieuList = $this->lieu->getList($type, $currentYear, $currentMonth, '', array_keys($depts), '', $orderBy);
                    }
                }
            }
        }
        elseif($type == 'company')
        {
            $lieuList = $this->lieu->getList($type, $currentYear, $currentMonth, '', '', '', $orderBy);
        }

        $this->view->title    = $this->lang->lieu->browse;
        $this->view->type     = $type;
        $this->view->deptList = $deptList;
        $this->view->users    = $this->loadModel('user')->getPairs();
        $this->view->lieuList = $lieuList;
        $this->view->date     = $date;
        $this->view->orderBy  = $orderBy;
        $this->display();
    }

    /**
     * View a lieu.
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function view($id, $type = '')
    {
        $lieu = $this->lieu->getById($id);

        $overtimePairs = array();
        $overtimeList  = $this->loadModel('overtime', 'oa')->getByIdList(trim($lieu->overtime, ','));
        foreach($overtimeList as $overtime) 
        {
            $overtimePairs[$overtime->id] = formatTime($overtime->begin . ' ' . $overtime->start, DT_DATETIME2) . ' ~ ' . formatTime($overtime->end . ' ' . $overtime->finish, DT_DATETIME2);
        }

        $this->view->title         = $this->lang->lieu->view;
        $this->view->lieu          = $lieu;
        $this->view->users         = $this->loadModel('user', 'sys')->getPairs();
        $this->view->overtimePairs = $overtimePairs;
        $this->view->type          = $type;
        $this->display();
    }

    /**
     * Create lieu.
     * 
     * @param  string $date
     * @access public
     * @return void
     */
    public function create($date = '')
    {
        if($_POST)
        {
            $result = $this->lieu->create();
            if(is_array($result)) $this->send($result);

            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $lieuID   = $result;
            $actionID = $this->loadModel('action')->create('lieu', $lieuID, 'created');
            $this->sendmail($lieuID, $actionID);

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('personal')));
        }

        if($date)
        {
            $date = date('Y-m-d', strtotime($date));
            $lieu = $this->lieu->getByDate($date, $this->app->user->account);
            if($lieu && strpos(',wait,draft,', $lieu->status) !== false) $this->locate(inlink('edit', "id=$lieu->id"));
        }

        $overtimePairs = array('');
        $overtimeList  = $this->loadModel('overtime', 'oa')->getList('company', '', '', $this->app->user->account, '', 'pass');
        foreach($overtimeList as $key => $overtime)
        {
            $overtimePairs[$overtime->id] = formatTime($overtime->begin . ' ' . $overtime->start, DT_DATETIME2) . ' ~ ' . formatTime($overtime->end . ' ' . $overtime->finish, DT_DATETIME2);
        }

        $this->view->title         = $this->lang->lieu->create;
        $this->view->overtimePairs = $overtimePairs;
        $this->view->date          = $date;
        $this->display();
    }

    /**
     * Edit lieu.
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function edit($id)
    {
        $lieu = $this->lieu->getById($id);
        /* check privilage. */
        if($lieu->createdBy != $this->app->user->account) 
        {
            $locate     = helper::safe64Encode(helper::createLink('oa.lieu', 'browse'));
            $noticeLink = helper::createLink('notice', 'index', "type=accessLimited&locate={$locate}");
            die(js::locate($noticeLink));
        }

        if($_POST)
        {
            $result = $this->lieu->update($id);
            if(is_array($result) && $result['result'] == 'fail') $this->send($result);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            if($result)
            {
                $actionID = $this->loadModel('action')->create('lieu', $id, 'edited');
                $this->action->logHistory($actionID, $result);
            }
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $overtimePairs = array('');
        $overtimeList  = $this->loadModel('overtime', 'oa')->getList('company', '', '', $this->app->user->account, '', 'pass');
        foreach($overtimeList as $key => $overtime)
        {
            $overtimePairs[$overtime->id] = formatTime($overtime->begin . ' ' . $overtime->start, DT_DATETIME2) . ' ~ ' . formatTime($overtime->end . ' ' . $overtime->finish, DT_DATETIME2);
        }

        $this->view->title         = $this->lang->lieu->edit;
        $this->view->overtimePairs = $overtimePairs;
        $this->view->lieu          = $lieu;
        $this->display();
    }

    /**
     * Delete lieu.
     * 
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function delete($id)
    {
        $lieu = $this->lieu->getById($id);
        if($lieu->createdBy != $this->app->user->account) $this->send(array('result' => 'fail', 'message' => $this->lang->lieu->denied));

        $this->lieu->delete($id);
        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
        $this->send(array('result' => 'success'));
    }

    /**
     * Cancel or commit a lieu.
     * 
     * @param  int    $lieuID 
     * @access public
     * @return void
     */
    public function switchStatus($lieuID)
    {
        $lieu = $this->lieu->getById($lieuID);
        if(!$lieu) return false;

        if($lieu->createdBy != $this->app->user->account) $this->send(array('result' => 'fail', 'message' => $this->lang->liue->denied));

        if($lieu->status == 'wait')
        {
            $this->dao->update(TABLE_LIEU)->set('status')->eq('draft')->where('id')->eq($lieuID)->exec();

            $actionID = $this->loadModel('action')->create('lieu', $lieuID, 'revoked');
            $this->sendmail($lieuID, $actionID);
        }

        if($lieu->status == 'draft')
        {
            $this->dao->update(TABLE_LIEU)->set('status')->eq('wait')->where('id')->eq($lieuID)->exec();

            $actionID = $this->loadModel('action')->create('lieu', $lieuID, 'commited');
            $this->sendmail($lieuID, $actionID);
        }

        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
        $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess));
    }

    /**
     * review 
     * 
     * @param  int    $id 
     * @param  string $status 
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
            $reviewedBy = $this->lieu->getReviewedBy();
            if($reviewedBy)
            { 
                if($reviewedBy == $this->app->user->account) $canReview = true;
            }
            else
            {
                $lieu        = $this->lieu->getById($id);
                $createdUser = $this->loadModel('user')->getByAccount($lieu->createdBy);
                $dept        = $this->loadModel('tree')->getById($createdUser->dept);
                if($dept && $this->app->user->account == trim($dept->moderators, ',')) $canReview = true;
            }
        }

        if($status == 'pass')
        {
            if(!$canReview) $this->send(array('result' => 'fail', 'message' => $this->lang->lieu->denied));

            $this->lieu->review($id, $status);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $actionID = $this->loadModel('action')->create('lieu', $id, 'reviewed', '', $this->lang->lieu->statusList[$status]);
            $this->sendmail($id, $actionID);

            $this->send(array('result' => 'success'));
        }
        
        if($status == 'reject')
        {
            if($_POST)
            {
                if(!$canReview) $this->send(array('result' => 'fail', 'message' => $this->lang->lieu->denied));

                if(!$this->post->comment) $this->send(array('result' => 'fail', 'message' => array('comment' => sprintf($this->lang->error->notempty, $this->lang->lieu->rejectReason))));

                $this->lieu->review($id, $status);
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

                $actionID = $this->loadModel('action')->create('lieu', $id, 'reviewed', $this->post->comment, $this->lang->lieu->statusList[$status]);
                $this->sendmail($id, $actionID);

                $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
            }

            $this->view->title = $this->lang->lieu->review;
            $this->view->id    = $id;
            $this->display();
        }
    }

    /**
     * Batch review lieus. 
     * 
     * @param  string $status
     * @access public
     * @return void
     */
    public function batchReview($status = 'pass')
    {
        if(!$this->post->lieuIDList) $this->send(array('result' => 'fail', 'message' => $this->lang->lieu->nodata));

        /* Check privilage. */
        $canReview  = false;
        $lieuIDList = $this->post->lieuIDList;
        if($this->app->user->admin == 'super')
        {
            $canReview = true;
        }
        else
        {
            $reviewedBy = $this->lieu->getReviewedBy();
            if($reviewedBy)
            { 
                if($reviewedBy == $this->app->user->account) $canReview = true;
            }
            else
            {
                $lieuIDList = $this->dao->select('t1.id')->from(TABLE_LIEU)->alias('t1')
                    ->leftJoin(TABLE_USER)->alias('t2')->on('t1.createdBy=t2.account')
                    ->leftJoin(TABLE_CATEGORY)->alias('t3')->on('t2.dept=t3.id')
                    ->where('t1.id')->in($lieuIDList)
                    ->andWhere('t3.type')->eq('dept')
                    ->andWhere('t3.moderators')->eq(",{$this->app->user->account},")
                    ->fetchPairs();
                if($lieuIDList) $canReview = true;
            }
        }

        if($status == 'pass')
        {
            if(!$canReview) $this->send(array('result' => 'fail', 'message' => $this->lang->lieu->denied));

            foreach($lieuIDList as $id)
            {
                $this->lieu->review($id, $status);
                if(dao::isError()) continue;

                $actionID = $this->loadModel('action')->create('lieu', $id, 'reviewed', '', $this->lang->lieu->statusList[$status]);
                $this->sendmail($id, $actionID);
            }
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->send(array('result' => 'success', 'message' => $this->lang->leave->reviewSuccess, 'locate' => 'reload'));
        }
    }

    /**
     * Send email.
     * 
     * @param  int    $lieuID 
     * @param  int    $actionID 
     * @access public
     * @return void
     */
    public function sendmail($lieuID, $actionID)
    {
        /* Reset $this->output. */
        $this->clear();

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Set toList and ccList. */
        $lieu   = $this->lieu->getById($lieuID);
        $users  = $this->loadModel('user')->getPairs();
        $toList = '';
        if($action->action == 'reviewed')
        {
            $toList = $lieu->createdBy;
            $subject = "{$this->lang->lieu->common}{$this->lang->lieu->statusList[$lieu->status]}#{$lieu->id} " . zget($users, $lieu->createdBy);
        }
        if($action->action == 'created' or $action->action == 'revoked' or $action->action == 'commited')
        {
            $reviewedBy = $this->lieu->getReviewedBy();
            if($reviewedBy)
            {
                $toList = $reviewedBy; 
            }
            else
            {
               $dept   = $this->loadModel('tree')->getById($this->app->user->dept);
               $toList = isset($dept->moderators) ? trim($dept->moderators, ',') : '';
            }

            $subject = "{$this->lang->lieu->common}#{$lieu->id} " . zget($users, $lieu->createdBy);
        }

        /* send notice if user is online and return failed accounts. */
        $toList = $this->loadModel('action')->sendNotice($actionID, $toList);

        /* Create the email content. */
        $this->view->lieu   = $lieu;
        $this->view->action = $action;
        $this->view->users  = $users;

        $mailContent = $this->parse($this->moduleName, 'sendmail');

        /* Send emails. */
        $this->loadModel('mail')->send($toList, $subject, $mailContent);
        if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
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
            $this->loadModel('setting')->setItem('system.oa.lieu..reviewedBy', $this->post->reviewedBy);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess));
        }

        if($module)
        {
            $this->lang->menuGroups->lieu = $module;
            $this->lang->lieu->menu       = $this->lang->$module->menu;
        }

        $this->view->title      = $this->lang->lieu->setReviewer;
        $this->view->users      = $this->loadModel('user', 'sys')->getPairs('noclosed,noforbidden,nodeleted');
        $this->view->reviewedBy = $this->lieu->getReviewedBy();
        $this->display();
    }
}
