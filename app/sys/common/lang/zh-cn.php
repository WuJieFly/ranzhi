<?php
/**
 * The zh-cn file of common module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     common 
 * @version     $Id: zh-cn.php 4194 2016-10-21 09:23:53Z daitingting $
 * @link        http://www.ranzhico.com
 */
$lang->colon      = ' : ';
$lang->ellipsis   = '…';
$lang->prev       = '‹';
$lang->next       = '›';
$lang->unfold     = '+';
$lang->fold       = '-';
$lang->percent    = '%';
$lang->laquo      = '&laquo;';
$lang->raquo      = '&raquo;';
$lang->minus      = ' - ';
$lang->hyphen     = '-';
$lang->slash      = ' / ';
$lang->RMB        = '￥';
$lang->divider    = "<span class='divider'>{$lang->raquo}</span> ";
$lang->at         = ' 于 ';
$lang->by         = ' 由 ';
$lang->ditto      = '同上';
$lang->etc        = '等';
$lang->importIcon = "<i class='icon-download-alt'> </i>";
$lang->exportIcon = "<i class='icon-upload-alt'> </i>";

/* Apps lang items.*/
$lang->apps = new stdclass();
$lang->apps->crm        = '客户';
$lang->apps->cash       = '财务';
$lang->apps->oa         = '办公';
$lang->apps->doc        = '文档';
$lang->apps->proj       = '项目';
$lang->apps->sys        = '通用';
$lang->apps->team       = '团队';
$lang->apps->superadmin = '后台';

/* Lang items for ranzhi. */
$lang->ranzhi    = '然之协同';
$lang->agreement = "已阅读并同意<a href='http://zpl.pub/page/zplv11.html' target='_blank'>《Z PUBLIC LICENSE授权协议1.2》</a>。<span class='text-danger'>未经许可，不得去除、隐藏或遮掩然之系统的任何标志及链接。</span>";
$lang->poweredBy = "<a href='http://www.ranzhico.com/?v=%s' target='_blank'>{$lang->ranzhi}%s</a>";
$lang->ipLimited = "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head><body>抱歉，管理员限制当前IP登录，请联系管理员解除限制。</body></html>";

/* IE6 alert.  */
$lang->IE6Alert = <<<EOT
    <div class='alert alert-danger' style='margin-top:100px;'>
      <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
      <h2>请使用其他浏览器访问本站。</h2>
      <p>珍爱上网，远离IE！</p>
      <p>我们检测到您正在使用Internet Explorer 6 ——  IE6 浏览器, IE6 于2001年8月27日推出，而现在它已十分脱节。速度慢、不安全、不能很好的展示新一代网站。<br/></p>
      <a href='https://www.google.com/intl/zh-hk/chrome/browser/' class='btn btn-primary btn-lg' target='_blank'>谷歌浏览器</a>
      <a href='http://www.firefox.com/' class='btn btn-primary btn-lg' target='_blank'>火狐浏览器</a>
      <a href='http://www.opera.com/download' class='btn btn-primary btn-lg' target='_blank'>Opera浏览器</a>
      <p></p>
    </div>
EOT;

/* Themes. */
$lang->theme             = '主题';
$lang->themes['default'] = '默认';
$lang->themes['clear']   = '清晰';

/* Global lang items. */
$lang->home           = '首页';
$lang->welcome        = "%s协同管理系统";
$lang->messages       = "<strong><i class='icon-comment-alt'></i> %s</strong>";
$lang->todayIs        = '今天是%s，';
$lang->today          = '今天';
$lang->aboutUs        = '关于我们';
$lang->about          = '关于';
$lang->link           = '友情链接';
$lang->frontHome      = '前台';
$lang->forumHome      = '论坛';
$lang->bookHome       = '手册';
$lang->register       = '注册';
$lang->logout         = '退出';
$lang->login          = '登录';
$lang->account        = '帐号';
$lang->password       = '密码';
$lang->all            = '全部';
$lang->changePassword = '修改密码';
$lang->currentPos     = '当前位置';
$lang->categoryMenu   = '分类导航';
$lang->basicInfo      = '基本信息';

/* Global action items. */
$lang->reset          = '重填';
$lang->add            = '添加';
$lang->edit           = '编辑';
$lang->copy           = '复制';
$lang->and            = '并且';
$lang->or             = '或者';
$lang->hide           = '隐藏';
$lang->delete         = '删除';
$lang->close          = '关闭';
$lang->finish         = '完成';
$lang->cancel         = '取消';
$lang->import         = '导入';
$lang->export         = '导出';
$lang->setFileName    = '文件名';
$lang->setFileNum     = '记录数';
$lang->setFileType    = '文件类型';
$lang->setCharset     = '编码格式';
$lang->save           = '保存';
$lang->saved          = '已保存';
$lang->confirm        = '确认';
$lang->preview        = '预览';
$lang->goback         = '返回';
$lang->assign         = '指派';
$lang->start          = '开始';
$lang->create         = '新建';
$lang->forbid         = '禁用';
$lang->activate       = '激活';
$lang->ignore         = '忽略';
$lang->view           = '查看';
$lang->detail         = '详情';
$lang->more           = '更多';
$lang->actions        = '操作';
$lang->history        = '历史记录';
$lang->reverse        = '切换顺序';
$lang->switchDisplay  = '切换显示';
$lang->feature        = '未来';
$lang->year           = '年';
$lang->month          = '月';
$lang->day            = '日';
$lang->loading        = '稍候...';
$lang->saveSuccess    = '保存成功';
$lang->setSuccess     = '设置成功';
$lang->sendSuccess    = '发送成功';
$lang->fail           = '失败';
$lang->noResultsMatch = '没有匹配的选项';
$lang->searchMore     = "搜索此关键字的更多结果：";
$lang->files          = '附件';
$lang->addFiles       = '上传了附件 ';
$lang->comment        = '备注';
$lang->selectAll      = '全选';
$lang->selectReverse  = '反选';
$lang->continueSave   = '继续保存';
$lang->submitting     = '稍候...';
$lang->yes            = '是';
$lang->no             = '否';
$lang->signIn         = '签到';
$lang->signOut        = '签退';
$lang->sort           = '排序';
$lang->required       = '必填';
$lang->custom         = '自定义';

$lang->exportAll      = '导出全部记录';
$lang->exportThisPage = '导出本页记录';
$lang->exportTemplate = '导出模板';
$lang->exportExcel    = '导出Excel';
$lang->exportWord     = '导出Word';
$lang->importFile     = '导入文件';
$lang->importSuccess  = '导入成功';
$lang->importFail     = '导入失败';

/* Items for lifetime. */
$lang->lifetime = new stdclass();
$lang->lifetime->createdBy    = '由谁创建';
$lang->lifetime->assignedTo   = '指派给';
$lang->lifetime->signedBy     = '由谁签约';
$lang->lifetime->closedBy     = '由谁关闭';
$lang->lifetime->closedReason = '关闭原因';
$lang->lifetime->lastEdited   = '最后修改';

$lang->setOkFile = <<<EOT
<h5>请按照下面的步骤操作以确认您的管理员身份。</h5>
<p>创建 %s 文件。如果存在该文件，使用编辑软件打开，重新保存一遍。</p>
EOT;

/* Items for javascript. */
$lang->js = new stdclass();
$lang->js->confirmDelete         = '您确定要执行删除操作吗？';
$lang->js->deleteing             = '删除中';
$lang->js->doing                 = '处理中';
$lang->js->timeout               = '网络超时,请重试';
$lang->js->confirmDiscardChanges = '表单已更改，确定关闭？';
$lang->js->yes                   = '是';
$lang->js->no                    = '否';

/* Contact fields*/
$lang->company = new stdclass();
$lang->company->contactUs = '联系我们';
$lang->company->address   = '地址';
$lang->company->phone     = '电话';
$lang->company->email     = 'Email';
$lang->company->fax       = '传真';
$lang->company->qq        = 'QQ';
$lang->company->weibo     = '微博';
$lang->company->weixin    = '微信';
$lang->company->wangwang  = '旺旺';

/* The main menus. */
$lang->menu = new stdclass();

$lang->index   = new stdclass();
$lang->user    = new stdclass();
$lang->file    = new stdclass();
$lang->admin   = new stdclass();
$lang->tree    = new stdclass();
$lang->mail    = new stdclass();
$lang->dept    = new stdclass();
$lang->thread  = new stdclass();
$lang->block   = new stdclass();
$lang->action  = new stdclass();
$lang->effort  = new stdclass();
$lang->setting = new stdclass();
$lang->task    = new stdclass();
$lang->schema  = new stdclass();
$lang->package = new stdclass();

$lang->admin->common = '后台管理';

$lang->menu->sys = new stdclass();
$lang->menu->sys->company = '公司|company|setbasic|';
$lang->menu->sys->user    = '组织|user|admin|';
$lang->menu->sys->group   = '权限|group|browse|';
$lang->menu->sys->entry   = '应用|entry|admin|';
$lang->menu->sys->system  = '系统|mail|admin|';
$lang->menu->sys->package = '扩展|package|browse|';

$lang->message = new stdclass(); 
$lang->blog    = new stdclass(); 
$lang->group   = new stdclass(); 

/* Menu entry. */
$lang->entry       = new stdclass();
$lang->entry->menu = new stdclass();
$lang->entry->menu->admin    = array('link' => '应用列表|entry|admin|', 'alias' => 'edit, integration, style, zentaoAdmin');
$lang->entry->menu->create   = '添加应用|entry|create|';
$lang->entry->menu->webapp   = 'WEB应用|webapp|obtain|';
$lang->entry->menu->category = '分组|entry|category|';

/* Menu system. */
$lang->system       = new stdclass();
$lang->system->menu = new stdclass();
$lang->system->menu->mail   = array('link' => '发信|mail|admin|', 'alias' => 'detect,edit,save,test');
$lang->system->menu->trash  = array('link' => '回收站|action|trash|');
$lang->system->menu->cron   = '计划任务|cron|index|';
$lang->system->menu->backup = '备份|backup|index|';

$lang->article = new stdclass();
$lang->article->menu = new stdclass();
$lang->article->menu->admin  = '浏览|article|admin|';
$lang->article->menu->tree   = '模块|tree|browse|type=article';
$lang->article->menu->create = array('link' => '添加文章|article|create|type=article', 'alias' => 'edit');

$lang->menuGroups = new stdclass();

$lang->menu->dashboard = new stdclass();
$lang->menu->dashboard->todo     = '待办|todo|calendar|';
$lang->menu->dashboard->task     = '任务|my|task|';
$lang->menu->dashboard->project  = '项目|my|project|';
$lang->menu->dashboard->order    = '订单|my|order|';
$lang->menu->dashboard->contract = '合同|my|contract|';
$lang->menu->dashboard->review   = '审批|my|review|';
$lang->menu->dashboard->company  = '组织|my|company|';
$lang->menu->dashboard->dynamic  = '动态|my|dynamic|';

/* Menu of customer module. */
if(!isset($lang->customer)) $lang->customer = new stdclass();
$lang->customer->menu = new stdclass();
$lang->customer->menu->browse     = '所有客户|customer|browse|mode=all';
$lang->customer->menu->assignedTo = '指派给我|customer|browse|mode=assignedTo';
$lang->customer->menu->past       = '亟需联系|customer|browse|mode=past';
$lang->customer->menu->today      = '今天联系|customer|browse|mode=today';
$lang->customer->menu->tomorrow   = '明天联系|customer|browse|mode=tomorrow';
$lang->customer->menu->thisweek   = '本周内联系|customer|browse|mode=thisweek';
$lang->customer->menu->thismonth  = '本月内联系|customer|browse|mode=thismonth';
$lang->customer->menu->public     = '客户池|customer|browse|mode=public';
$lang->customer->menu->report     = '报表|report|browse|module=customer';

/* Menu of provider module. */
if(!isset($lang->provider)) $lang->provider = new stdclass();
$lang->provider->menu = new stdclass();
$lang->provider->menu->browse = array('link' => '供应商列表|provider|browse|', 'alias' => 'create,edit,view');

/* Menu of product module. */
if(!isset($lang->product)) $lang->product = new stdclass();
$lang->product->menu = new stdclass();
$lang->product->menu->browse     = '所有产品|product|browse|mode=browse&status=all';
$lang->product->menu->normal     = '正常|product|browse|mode=browse&status=normal';
$lang->product->menu->developing = '研发中|product|browse|mode=browse&status=developing';
$lang->product->menu->offline    = '下线|product|browse|mode=browse&status=offline';

$lang->todo = new stdclass();
$lang->todo->menu = new stdclass();
$lang->todo->menu->calendar        = '日历|todo|calendar|';
$lang->todo->menu->all             = '所有|todo|browse|mode=all';
$lang->todo->menu->assignedToOther = '指派他人|todo|browse|mode=assignedtoother';
$lang->todo->menu->assignedToMe    = '指派给我|todo|browse|mode=assignedtome';
$lang->todo->menu->undone          = '未完成|todo|browse|mode=undone';
$lang->todo->menu->future          = '待定|todo|browse|mode=future';

$lang->my = new stdclass();
$lang->my->review = new stdclass();
$lang->my->review->menu = new stdclass();
$lang->my->review->menu->all      = '所有|my|review|type=all';
$lang->my->review->menu->attend   = '考勤|my|review|type=attend';
$lang->my->review->menu->leave    = '请假|my|review|type=leave';
$lang->my->review->menu->makeup   = '补班|my|review|type=makeup';
$lang->my->review->menu->overtime = '加班|my|review|type=overtime';
$lang->my->review->menu->lieu     = '调休|my|review|type=lieu';
$lang->my->review->menu->refund   = '报销|my|review|type=refund';

$lang->my->order = new stdclass();
$lang->my->order->menu = new stdclass();
$lang->my->order->menu->past       = '亟需联系|my|order|type=past';
$lang->my->order->menu->today      = '今天联系|my|order|type=today';
$lang->my->order->menu->tomorrow   = '明天联系|my|order|type=tomorrow';
$lang->my->order->menu->assignedTo = '指派给我|my|order|type=assignedTo';
$lang->my->order->menu->createdBy  = '由我创建|my|order|type=createdBy';
$lang->my->order->menu->signedBy   = '由我签约|my|order|type=signedBy';
$lang->my->order->menu->all        = '所有|my|order|type=all';
 
$lang->my->contract = new stdclass();
$lang->my->contract->menu = new stdclass();
$lang->my->contract->menu->unfinished  = '未完成|my|contract|type=unfinished';
$lang->my->contract->menu->finished    = '已完成|my|contract|type=finished';
$lang->my->contract->menu->canceled    = '已取消|my|contract|type=canceled';
$lang->my->contract->menu->returnedBy  = '由我回款|my|contract|type=returnedBy';
$lang->my->contract->menu->deliveredBy = '由我交付|my|contract|type=deliveredBy';

$lang->my->task = new stdclass();
$lang->my->task->menu = new stdclass();
$lang->my->task->menu->assignedToMe = '指派给我|my|task|type=assignedTo';
$lang->my->task->menu->createdByMe  = '由我创建|my|task|type=createdBy';
$lang->my->task->menu->finishedByMe = '由我完成|my|task|type=finishedBy';
$lang->my->task->menu->closedByMe   = '由我关闭|my|task|type=closedBy';
$lang->my->task->menu->canceledByMe = '由我取消|my|task|type=canceledBy';
$lang->my->task->menu->unclosed     = '未关闭|my|task|type=unclosed';

$lang->my->dynamic = new stdclass();
$lang->my->dynamic->menu = new stdclass();
$lang->my->dynamic->menu->today      = '今天|my|dynamic|period=today';
$lang->my->dynamic->menu->yesterday  = '昨天|my|dynamic|period=yesterday';
$lang->my->dynamic->menu->twodaysago = '前天|my|dynamic|period=twodaysago';
$lang->my->dynamic->menu->thisweek   = '本周|my|dynamic|period=thisweek';
$lang->my->dynamic->menu->lastweek   = '上周|my|dynamic|period=lastweek';
$lang->my->dynamic->menu->thismonth  = '本月|my|dynamic|period=thismonth';
$lang->my->dynamic->menu->lastmonth  = '上月|my|dynamic|period=lastmonth';
$lang->my->dynamic->menu->all        = '所有|my|dynamic|period=all';

$lang->my->company = new stdclass();

/* Menu of mail module. */
$lang->mail = new stdclass();
$lang->mail->menu = $lang->system->menu;
$lang->menuGroups->mail = 'system';

/* Menu of action module. */
$lang->action = new stdclass();
$lang->action->menu = $lang->system->menu;
$lang->menuGroups->action = 'system';

/* Menu of cron module. */
$lang->cron = new stdclass();
$lang->cron->menu = $lang->system->menu;
$lang->menuGroups->cron = 'system';

/* Menu of backup module. */
$lang->backup = new stdclass();
$lang->backup->menu = $lang->system->menu;
$lang->menuGroups->backup = 'system';

/* The error messages. */
$lang->error = new stdclass();
$lang->error->length       = array('<strong>%s</strong>长度错误，应当为<strong>%s</strong>', '<strong>%s</strong>长度应当不超过<strong>%s</strong>，且不小于<strong>%s</strong>。');
$lang->error->reg          = '<strong>%s</strong>不符合格式，应当为:<strong>%s</strong>。';
$lang->error->unique       = '<strong>%s</strong>已经有<strong>%s</strong>这条记录了。';
$lang->error->notempty     = '<strong>%s</strong>不能为空。';
$lang->error->empty        = "<strong>%s</strong>必须为空。";
$lang->error->equal        = '<strong>%s</strong>必须为<strong>%s</strong>。';
$lang->error->gt           = "<strong>%s</strong>应当大于<strong>%s</strong>。";
$lang->error->ge           = "<strong>%s</strong>应当不小于<strong>%s</strong>。";
$lang->error->lt           = "<strong>%s</strong>应当小于<strong>%s</strong>。";
$lang->error->le           = "<strong>%s</strong>应当不大于<strong>%s</strong>。";
$lang->error->in           = '<strong>%s</strong>必须为<strong>%s</strong>。';
$lang->error->int          = array('<strong>%s</strong>应当是数字。', '<strong>%s</strong>最小值为%s',  '<strong>%s</strong>应当介于<strong>%s-%s</strong>之间。');
$lang->error->float        = '<strong>%s</strong>应当是数字，可以是小数。';
$lang->error->email        = '<strong>%s</strong>应当为合法的EMAIL。';
$lang->error->URL          = '<strong>%s</strong>应当为合法的URL。';
$lang->error->date         = '<strong>%s</strong>应当为合法的日期。';
$lang->error->code         = '<strong>%s</strong>应当为字母或数字的组合。';
$lang->error->account      = '<strong>%s</strong>应当为字母或数字的组合，至少三位';
$lang->error->passwordsame = '两次密码应当相等。';
$lang->error->passwordrule = '密码应该符合规则，长度至少为六位。';
$lang->error->captcha      = '请输入正确的验证码。';
$lang->error->noWritable   = '%s 可能不可写，请修改权限！';
$lang->error->noConvertFun = '不存在iconv和mb_convert_encoding转码方法，不能将数据转成想要的编码！';
$lang->error->noCurlExt    = '没有加载curl扩展！';
$lang->error->notInt       = '<strong>%s</strong>不能为纯数字组合。';
$lang->error->pasteImg     = '您的浏览器不支持粘贴图片！';

/* The pager items. */
$lang->pager = new stdclass();
$lang->pager->noRecord   = '暂时没有记录。';
$lang->pager->digest     = "共 <strong>%s</strong> 条记录，%s <strong>%s/%s</strong> &nbsp; ";
$lang->pager->recPerPage = "每页 <strong>%s</strong> 条";
$lang->pager->first      = '首页';
$lang->pager->pre        = '上页';
$lang->pager->next       = '下页';
$lang->pager->last       = '末页';
$lang->pager->locate     = 'Go!';
$lang->pager->showMore   = '显示更多 <i class="icon icon-double-angle-down"></i>';
$lang->pager->noMore     = '没有更多';
$lang->pager->showTotal  = '已显示 <strong>%s</strong> 项，共 <strong>%s</strong> 项';
$lang->pager->previousPage = "上一页";
$lang->pager->nextPage     = "下一页";
$lang->pager->summery      = "第 <strong>%s-%s</strong> 项，共 <strong>%s</strong> 项";

/* The excel items. */
$lang->excel = new stdClass();
$lang->excel->canNotRead = '不能解析该文件';

$lang->excel->error = new stdclass();
$lang->excel->error->info   = '您输入的值不在下拉框列表内。';
$lang->excel->error->title  = '输入有误';
$lang->excel->error->noFile = '没有文件';
$lang->excel->error->noData = '没有有效的数据';

$lang->excel->title = new stdclass();
$lang->excel->title->contact  = '联系人';
$lang->excel->title->sysValue = '系统数据';

$lang->excel->help = new stdclass();
$lang->excel->help->contact = "“真实姓名“是必填字段，如果不填导入时会忽略这条数据。";

$lang->date = new stdclass();
$lang->date->minute = '分钟';
$lang->date->day    = '天';

$lang->genderList = new stdclass();
$lang->genderList->m = '男';
$lang->genderList->f = '女';
$lang->genderList->u = '';

/* datepicker 时间*/
$lang->datepicker = new stdclass();

$lang->datepicker->dpText = new stdclass();
$lang->datepicker->dpText->TEXT_OR          = '或 ';
$lang->datepicker->dpText->TEXT_PREV_YEAR   = '去年';
$lang->datepicker->dpText->TEXT_PREV_MONTH  = '上月';
$lang->datepicker->dpText->TEXT_PREV_WEEK   = '上周';
$lang->datepicker->dpText->TEXT_YESTERDAY   = '昨天';
$lang->datepicker->dpText->TEXT_THIS_YEAR   = '今年';
$lang->datepicker->dpText->TEXT_THIS_MONTH  = '本月';
$lang->datepicker->dpText->TEXT_THIS_WEEK   = '本周';
$lang->datepicker->dpText->TEXT_TODAY       = '今天';
$lang->datepicker->dpText->TEXT_NEXT_YEAR   = '明年';
$lang->datepicker->dpText->TEXT_NEXT_MONTH  = '下月';
$lang->datepicker->dpText->TEXT_CLOSE       = '关闭';
$lang->datepicker->dpText->TEXT_DATE        = '选择时间段';
$lang->datepicker->dpText->TEXT_CHOOSE_DATE = '选择日期';

$lang->datepicker->dayNames     = array('星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六');
$lang->datepicker->abbrDayNames = array('日', '一', '二', '三', '四', '五', '六');
$lang->datepicker->monthNames   = array('一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月');

/* Set currency items. */
$lang->currencyList['rmb']  = '人民币';
$lang->currencyList['usd']  = '美元';
$lang->currencyList['hkd']  = '港元';
$lang->currencyList['twd']  = '台元';
$lang->currencyList['euro'] = '欧元';
$lang->currencyList['dem']  = '马克';
$lang->currencyList['chf']  = '瑞士法郎';
$lang->currencyList['frf']  = '法国法郎';
$lang->currencyList['gbp']  = '英镑';
$lang->currencyList['nlg']  = '荷兰盾';
$lang->currencyList['cad']  = '加拿大元';
$lang->currencyList['sur']  = '卢布';
$lang->currencyList['inr']  = '卢比';
$lang->currencyList['aud']  = '澳大利亚元';
$lang->currencyList['nzd']  = '新西兰元';
$lang->currencyList['thb']  = '泰国铢';
$lang->currencyList['sgd']  = '新加坡元';

/* Currency symbols setting. */
$lang->currencySymbols['rmb']  = '￥';
$lang->currencySymbols['usd']  = '$';
$lang->currencySymbols['hkd']  = 'HK$';
$lang->currencySymbols['twd']  = 'NT$';
$lang->currencySymbols['euro'] = 'ECU';
$lang->currencySymbols['dem']  = 'DM';
$lang->currencySymbols['chf']  = 'SF';
$lang->currencySymbols['frf']  = 'FF';
$lang->currencySymbols['gbp']  = '￡';
$lang->currencySymbols['nlg']  = 'F';
$lang->currencySymbols['cad']  = 'CAN$';
$lang->currencySymbols['sur']  = 'Rbs';
$lang->currencySymbols['inr']  = 'Rs';
$lang->currencySymbols['aud']  = 'A$';
$lang->currencySymbols['nzd']  = 'NZ$';
$lang->currencySymbols['thb']  = 'B';
$lang->currencySymbols['sgd']  = 'S$';

$lang->currencyTip['w'] = '万';
$lang->currencyTip['y'] = '亿';

/* The datetime settings. */
define('DT_DATETIME1',  'Y-m-d H:i:s');
define('DT_DATETIME2',  'Y-m-d H:i');
define('DT_DATETIME3',  'y-m-d H:i');
define('DT_MONTHTIME1', 'n/d H:i');
define('DT_MONTHTIME2', 'n月d日 H:i');
define('DT_DATE1',      'Y-m-d');
define('DT_DATE2',      'Ymd');
define('DT_DATE3',      'Y年m月d日');
define('DT_DATE4',      'n月j日');
define('DT_DATE5',      'Y年m月');
define('DT_TIME1',      'H:i:s');
define('DT_TIME2',      'H:i');

include (dirname(__FILE__) . '/menuOrder.php');
