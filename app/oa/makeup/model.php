<?php
/**
 * The model file of makeup module of Ranzhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     makeup
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
class makeupModel extends model
{
    /**
     * Get a makeup by id. 
     * 
     * @param  int    $id 
     * @access public
     * @return object
     */
    public function getById($id)
    {
        return $this->dao->select('*')->from(TABLE_OVERTIME)->where('type')->eq('compensate')->andWhere('id')->eq($id)->fetch();
    }

    /**
     * Get makeup list. 
     * 
     * @param  string $type 
     * @param  string $year 
     * @param  string $month 
     * @param  string $account 
     * @param  string $dept 
     * @param  string $status 
     * @param  string $orderBy
     * @access public
     * @return array
     */
    public function getList($type = 'personal', $year = '', $month = '', $account = '', $dept = '', $status = '', $orderBy = 'id_desc')
    {
        $makeupList = $this->dao->select('t1.*, t2.realname, t2.dept')
            ->from(TABLE_OVERTIME)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on("t1.createdBy=t2.account")
            ->where("t1.type='compensate'")
            ->beginIf($year != '')->andWhere('t1.year')->eq($year)->fi()
            ->beginIf($month != '')->andWhere('t1.begin')->like("%-$month-%")->fi()
            ->beginIf($account != '')->andWhere('t1.createdBy')->eq($account)->fi()
            ->beginIf($dept != '')->andWhere('t2.dept')->in($dept)->fi()
            ->beginIf($status != '')->andWhere('t1.status')->eq($status)->fi()
            ->beginIf($type == 'browseReview')->andWhere('t1.status')->eq('wait')->fi()
            ->beginIf($type == 'company')->andWhere('t1.status')->ne('draft')->fi()
            ->orderBy("t2.dept,t1.{$orderBy}")
            ->fetchAll();
        $this->session->set('makeupQueryCondition', $this->dao->get());

        return $makeupList;
    }

    /**
     * Get makeup by date and account.
     * 
     * @param  string    $date 
     * @param  string    $account 
     * @access public
     * @return object
     */
    public function getByDate($date, $account)
    {
        $makeups = $this->dao->select('*')->from(TABLE_OVERTIME)->where('type')->eq('compensate')->andWhere('begin')->le($date)->andWhere('end')->ge($date)->andWhere('createdBy')->eq($account)->fetchAll();
        if(count($makeups) == 1) return current($makeups);
        return null;
    }

    /**
     * Get all month of makeup's begin.
     * 
     * @param  string $type
     * @access public
     * @return array
     */
    public function getAllMonth($type)
    {
        $monthList = array();
        $dateList  = $this->dao->select('begin')->from(TABLE_OVERTIME)
            ->where('type')->eq('compensate')
            ->beginIF($type == 'personal')->andWhere('createdBy')->eq($this->app->user->account)->fi()
            ->groupBy('begin')
            ->orderBy('begin_desc')
            ->fetchAll('begin');
        foreach($dateList as $date)
        {
            $year  = substr($date->begin, 0, 4);
            $month = substr($date->begin, 5, 2);
            if(!isset($monthList[$year][$month])) $monthList[$year][$month] = $month;
        }
        return $monthList;
    }

    /**
     * Get reviewed by. 
     * 
     * @access public
     * @return string
     */
    public function getReviewedBy()
    {
        $this->app->loadModuleConfig('attend');
        return !isset($this->config->makeup->reviewedBy) ? (!isset($this->config->attend->reviewedBy) ? '' : $this->config->attend->reviewedBy) : $this->config->makeup->reviewedBy;
    }

    /**
     * Create a makeup.
     * 
     * @access public
     * @return bool
     */
    public function create()
    {
        $makeup = fixer::input('post')
            ->add('type', 'compensate')
            ->add('status', 'wait')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::now())
            ->join('leave', ',')
            ->get();

        $makeup->leave = isset($makeup->leave) ? ',' . trim($makeup->leave, ',') . ',' : '';
        if(isset($makeup->begin) and $makeup->begin != '') $makeup->year = substr($makeup->begin, 0, 4);

        $return = $this->checkDate($makeup);
        if($return['result'] == 'fail') return $return;

        $this->dao->insert(TABLE_OVERTIME)
            ->data($makeup)
            ->autoCheck()
            ->batchCheck($this->config->makeup->require->create, 'notempty')
            ->check('end', 'ge', $makeup->begin)
            ->exec();

        return $this->dao->lastInsertID();
    }

    /**
     * update makeup.
     * 
     * @param  int    $id 
     * @access public
     * @return bool
     */
    public function update($id)
    {
        $oldMakeup = $this->getById($id);

        $makeup = fixer::input('post')
            ->add('type', 'compensate')
            ->remove('status')
            ->remove('createdBy')
            ->remove('createdDate')
            ->join('leave', ',')
            ->get();

        $makeup->leave = isset($makeup->leave) ? ',' . trim($makeup->leave, ',') . ',' : '';
        if(isset($makeup->begin) and $makeup->begin != '') $makeup->year = substr($makeup->begin, 0, 4);
        if($oldMakeup->status == 'reject') $makeup->status = 'wait';

        $return = $this->checkDate($makeup, $id);
        if($return['result'] == 'fail') return $return;

        $this->dao->update(TABLE_OVERTIME)
            ->data($makeup)
            ->autoCheck()
            ->batchCheck($this->config->makeup->require->edit, 'notempty')
            ->check('end', 'ge', $makeup->begin)
            ->where('id')->eq($id)
            ->exec();

        return commonModel::createChanges($oldMakeup, $makeup);
    }

    /**
     * Check date.
     * 
     * @param  object $date
     * @param  int    $id 
     * @access public
     * @return void
     */
    public function checkDate($date, $id = 0)
    {
        if(substr($date->begin, 0, 7) != substr($date->end, 0, 7)) return array('result' => 'fail', 'message' => $this->lang->makeup->sameMonth);
        if("$date->end $date->finish" <= "$date->begin $date->start") return array('result' => 'fail', 'message' => $this->lang->makeup->wrongEnd);

        $existMakeup = $this->checkMakeup($date, $this->app->user->account, $id);
        if(!empty($existMakeup)) return array('result' => 'fail', 'message' => sprintf($this->lang->makeup->unique, implode(', ', $existMakeup))); 

        $existLeave = $this->loadModel('leave', 'oa')->checkLeave($date, $this->app->user->account);
        if(!empty($existLeave)) return array('result' => 'fail', 'message' => sprintf($this->lang->leave->unique, implode(', ', $existLeave))); 

        $existOvertime = $this->loadModel('overtime', 'oa')->checkOvertime($date, $this->app->user->account);
        if(!empty($existOvertime)) return array('result' => 'fail', 'message' => sprintf($this->lang->overtime->unique, implode(', ', $existOvertime))); 
        
        $existTrip = $this->loadModel('trip', 'oa')->checkTrip('trip', $date, $this->app->user->account); 
        if(!empty($existTrip)) return array('result' => 'fail', 'message' => sprintf($this->lang->trip->unique, implode(', ', $existTrip))); 
        
        $this->app->loadLang('egress', 'oa');
        $existEgress = $this->trip->checkTrip('egress', $date, $this->app->user->account); 
        if(!empty($existEgress)) return array('result' => 'fail', 'message' => sprintf($this->lang->egress->unique, implode(', ', $existEgress))); 

        $existLieu = $this->loadModel('lieu', 'oa')->checkLieu($date, $this->app->user->account);
        if(!empty($existLieu)) return array('result' => 'fail', 'message' => sprintf($this->lang->lieu->unique, implode(', ', $existLieu)));  

        return array('result' => 'success');
    }

    /**
     * Check makeup.
     * 
     * @param  object $currentMakeup
     * @param  string $account 
     * @param  int    $id
     * @access public
     * @return bool 
     */
    public function checkMakeup($currentMakeup = null, $account = '', $id = 0)
    {
        $beginTime   = date('Y-m-d H:i:s', strtotime($currentMakeup->begin . ' ' . $currentMakeup->start));
        $endTime     = date('Y-m-d H:i:s', strtotime($currentMakeup->end   . ' ' . $currentMakeup->finish));
        $makeupList  = $this->getList($type = '', $year = '', $month = '', $account, $dept = '', $status = '', $orderBy = 'begin, start');
        $existMakeup = array();
        foreach($makeupList as $makeup)
        {
            if($makeup->id == $id) continue;
            if($makeup->status == 'reject') continue;

            $begin = $makeup->begin . ' ' . $makeup->start;
            $end   = $makeup->end   . ' ' . $makeup->finish;
            if(($beginTime > $begin && $beginTime < $end) 
                || ($endTime > $begin && $endTime < $end) 
                || ($beginTime <= $begin && $endTime >= $end))
            {
                $existMakeup[] = substr($begin, 0, 16) . ' ~ ' . substr($end, 0, 16);
            }
        }
        return $existMakeup;
    }

    /**
     * delete makeup.
     * 
     * @param  int    $id 
     * @access public
     * @return bool
     */
    public function delete($id, $null = null)
    {
        $oldMakeup = $this->getById($id);
        $this->dao->delete()->from(TABLE_OVERTIME)->where('id')->eq($id)->exec();

        if(!dao::isError())
        {
            $oldDates = range(strtotime($oldMakeup->begin), strtotime($oldMakeup->end), 60 * 60 * 24);
            $this->loadModel('attend', 'oa')->batchUpdate($oldDates, $oldMakeup->createdBy, '');
        }
        return !dao::isError();
    }

    /**
     * Review an makeup.
     * 
     * @param  int     $id 
     * @param  string  $status 
     * @access public
     * @return bool
     */
    public function review($id, $status)
    {
        if(!isset($this->lang->makeup->statusList[$status])) return false;

        $data = new stdclass();
        $data->status       = $status;
        $data->reviewedBy   = $this->app->user->account;
        $data->reviewedDate = helper::now();
        $data->rejectReason = $status == 'reject' ? $this->post->comment: '';

        $this->dao->update(TABLE_OVERTIME)->data($data)->autoCheck()->where('id')->eq($id)->exec();

        if(!dao::isError() and $status == 'pass')
        {
            $makeup = $this->getById($id);
            $dates  = range(strtotime($makeup->begin), strtotime($makeup->end), 60 * 60 * 24);
            $this->loadModel('attend', 'oa')->batchUpdate($dates, $makeup->createdBy, 'makeup', '', $makeup);
        }

        return !dao::isError();
    }

    /**
     * check date is in makeup. 
     * 
     * @param  string $date 
     * @param  string $account 
     * @access public
     * @return bool
     */
    public function isMakeup($date, $account)
    {
        static $makeupList = array();
        if(!isset($makeupList[$account])) $makeupList[$account] = $this->getList($type = 'company', $year = '', $month = '', $account, $dept = '', 'pass');

        foreach($makeupList[$account] as $makeup)
        {
            if(($makeup->status == 'pass') and strtotime($date) >= strtotime($makeup->begin) and strtotime($date) <= strtotime($makeup->end)) return true;
        }

        return false;
    }
}
