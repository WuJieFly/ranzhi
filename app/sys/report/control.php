<?php
/**
 * The control file of report module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     report
 * @version     $Id: control.php 4622 2013-03-28 01:09:02Z chencongzhi520@gmail.com $
 * @link        http://www.ranzhico.com
 */
class report extends control
{
    /**
     * browse report.
     * 
     * @param  tring $module 
     * @access public
     * @return void
     */
    public function browse($module)
    {
        if(!isset($this->config->report->moduleList[$module])) die('no this module.');
        $tableName     = $this->config->report->moduleList[$module];
        $checkedCharts = array();
        $charts        = array();
        $datas         = array();
        $tips          = array();

        /* Set menu. */
        $this->lang->report->menu = $this->lang->{$module}->menu;
        $this->lang->menuGroups->report = $module;

        if($_POST)
        {
            $checkedCharts = $this->post->charts;
            foreach($checkedCharts as $chart)
            {
                if(!isset($this->config->report->{$module}->chartList[$chart])) continue;
                $groupBy = $this->config->report->{$module}->chartList[$chart];

                /* merge options. */
                $chartOption = clone $this->lang->report->options;
                $chartOption->item = $this->lang->report->{$module}->item[$chart];
                if(isset($this->lang->report->{$module}->xAxisName[$chart])) $chartOption->graph->xAxisName = $this->lang->report->{$module}->xAxisName[$chart];
                if(isset($this->lang->report->{$module}->chartList[$chart])) $chartOption->graph->caption   = $this->lang->report->{$module}->chartList[$chart];

                /* add charts for multi currency. */
                $currencyList = $this->loadModel('common', 'sys')->getCurrencyList();
                if(strpos($groupBy, '`real`') !== false or strpos($groupBy, 'amount') !== false or strpos($groupBy, 'money') !== false)
                {
                    $caption = $chartOption->graph->caption;
                    foreach($currencyList as $key => $currency)
                    {
                        $chartOption->graph->caption = $caption . "($currency)";
                        $chartData = $this->report->getChartData($module, $chart, $tableName, $groupBy, $key);
                        $sum       = $this->report->computeSum($chartData);
                        if(empty($chartData) or $sum == 0) continue;

                        $charts["$chart$key"] = $chartOption;
                        $datas["$chart$key"]  = $this->report->computePercent($chartData);

                        $tips['caption']["$chart$key"] = $chartOption->graph->caption;
                        $tips['item']["$chart$key"]    = $this->lang->report->{$module}->item[$chart];
                        $tips['value']["$chart$key"]   = $this->lang->report->{$module}->value[$chart];
                    }
                }
                else
                {
                    $chartData = $this->report->getChartData($module, $chart, $tableName, $groupBy);
                    $charts[$chart] = $chartOption;
                    $datas[$chart]  = $this->report->computePercent($chartData);

                    $tips['caption'][$chart] = $chartOption->graph->caption;
                    $tips['item'][$chart]    = $this->lang->report->{$module}->item[$chart];
                    $tips['value'][$chart]   = $this->lang->report->{$module}->value[$chart];
                }
            }
        }

        $this->view->title         = $this->lang->report->common . '#' . $this->lang->report->{$module}->common;
        $this->view->module        = $module;
        $this->view->chartList     = $this->lang->report->{$module}->chartList;
        $this->view->checkedCharts = $checkedCharts;
        $this->view->charts        = $charts;
        $this->view->datas         = $datas;
        $this->view->tips          = $tips;
        $this->display();
    }

	/**
	 * Send daily reminder mail. 
	 * 
	 * @access public
	 * @return void
	 */
	public function remind()
	{
		$orders = $tasks = $todos = $customers = $contractCounts = array();
		if($this->config->report->dailyreminder->order)    $orders    = $this->report->getUserOrders();
		if($this->config->report->dailyreminder->task)     $tasks     = $this->report->getUserTasks();
		if($this->config->report->dailyreminder->todo)     $todos     = $this->report->getUserTodos();
		if($this->config->report->dailyreminder->customer) $customers = $this->report->getUserCustomers();
		$contractCounts = $this->report->getUserContractCount();

		$reminder = array();

		$users = array_unique(array_merge(array_keys($orders), array_keys($tasks), array_keys($todos), array_keys($customers), array_keys($contractCounts)));
		if(!empty($users)) foreach($users  as $user) $reminder[$user] = new stdclass();
		if(!empty($orders))foreach($orders as $user => $userOrders) $reminder[$user]->orders = $userOrders;
		if(!empty($tasks)) foreach($tasks  as $user => $userTasks)  $reminder[$user]->tasks  = $userTasks;
		if(!empty($todos)) foreach($todos  as $user => $userTodos)  $reminder[$user]->todos  = $userTodos;
		if(!empty($customers)) foreach($customers as $user => $userCustomers) $reminder[$user]->customers = $userCustomers;
		if(!empty($contractCounts)) foreach($contractCounts as $user => $contractCount) $reminder[$user]->contractCount = $contractCount;

		$this->loadModel('mail');

		/* Check mail turnon.*/
		if(!$this->config->mail->turnon)
		{
			echo "You should turn on the Email feature first.\n";
			return false;
		}

		foreach($reminder as $user => $mail)
		{
			/* Reset $this->output. */
			$this->clear();

			$mailTitle   = $this->lang->report->mailTitle->begin;
			$mailTitle  .= isset($mail->orders) ? sprintf($this->lang->report->mailTitle->order,    count($mail->orders))    : '';
			$mailTitle  .= isset($mail->tasks)  ? sprintf($this->lang->report->mailTitle->task,     count($mail->tasks))     : '';
			$mailTitle  .= isset($mail->todos)  ? sprintf($this->lang->report->mailTitle->todo,     count($mail->todos))     : '';
			$mailTitle  .= isset($mail->customers) ? sprintf($this->lang->report->mailTitle->customer, count($mail->customers)) : '';
			$mailTitle  .= isset($mail->contractCount) ? sprintf($this->lang->report->mailTitle->contractCount, $mail->contractCount) : '';
			$mailTitle   = rtrim($mailTitle, ',');

			/* Get email content and title.*/
			$this->view->mail = $mail;
			$oldViewType = $this->viewType;
			if($oldViewType == 'json') $this->viewType = 'html';
			$mailContent = $this->parse('report', 'dailyreminder');
			$this->viewType == $oldViewType;

			/* Send email.*/
			echo date('Y-m-d H:i:s') . " sending to $user, ";
			$this->mail->send($user, $mailTitle, $mailContent, '', true);
			if($this->mail->isError())
			{
				echo "fail: \n" ;
				a($this->mail->getError());
			}
			echo "ok\n";
		}
	}
}
