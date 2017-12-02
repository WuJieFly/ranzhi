<?php
$filter = new stdclass();
$filter->rules   = new stdclass();
$filter->default = new stdclass(); 
$filter->trade   = new stdclass();
$filter->entry   = new stdclass();
$filter->file    = new stdclass();
$filter->sso     = new stdclass();
$filter->upgrade = new stdclass();
$filter->user    = new stdclass();
$filter->rss     = new stdclass();
$filter->doc     = new stdclass();
$filter->project = new stdclass();
$filter->my      = new stdclass();
$filter->todo    = new stdclass();
$filter->webapp  = new stdclass();
$filter->reply   = new stdclass();
$filter->thread  = new stdclass();

$filter->rules->md5        = '/^[a-z0-9]{32}$/';
$filter->rules->base64     = '/^[a-zA-Z0-9\+\/\=]+$/';
$filter->rules->checked    = '/^[0-9,]+$/';
$filter->rules->idList     = '/^[0-9\|]+$/';
$filter->rules->lang       = '/^[a-zA-Z_\-]+$/';
$filter->rules->any        = '/./';
$filter->rules->number     = '/^[0-9]+$/';
$filter->rules->orderBy    = '/^\w+_(desc|asc)$/i';
$filter->rules->word       = '/^\w+$/';
$filter->rules->paramName  = '/^[a-zA-Z0-9_\.]+$/';
$filter->rules->paramValue = '/^[a-zA-Z0-9=_,:\-\?\&`#+\^\/\.%\|\x7f-\xff]+$/';
$filter->rules->common     = '/^[a-zA-Z0-9_]+$/';
$filter->rules->character  = '/^[a-zA-Z_\-]+$/';
$filter->rules->browseType = '/^by[a-z]+$/';
$filter->rules->key        = '/^[a-z0-9]{32}+$/';
$filter->rules->path       = '/(^//.|^/|^[a-zA-Z])?:?/.+(/$)?/';
$filter->rules->callback   = '/^[a-zA-Z0-9=\&\%\.\/\-\:\?]+$/';

$filter->default->moduleName = 'code';
$filter->default->methodName = 'code';
$filter->default->paramName  = 'reg::paramName';
$filter->default->paramValue = 'reg::paramValue';

$filter->user->admin = new stdclass();
$filter->user->admin->paramValue['query'] = 'reg::any';

$filter->default->get['onlybody']              = 'equal::yes';
$filter->default->get['lang']                  = 'reg::lang';
$filter->default->get['HTTP_X_REQUESTED_WITH'] = 'equal::XMLHttpRequest';

$filter->default->cookie['lang']      = 'reg::lang';
$filter->default->cookie['theme']     = 'reg::common';
$filter->default->cookie['device']    = 'reg::common';
$filter->default->cookie['ra']        = 'reg::common';
$filter->default->cookie['rp']        = 'reg::common';
$filter->default->cookie['keepLogin'] = 'equal::on';

$filter->block->default->get['mode']            = 'reg::character';
$filter->block->default->get['blockid']         = 'reg::character';
$filter->block->default->get['hash']            = 'reg::md5';
$filter->block->default->get['sso']             = 'reg::word';
$filter->block->default->get['app']             = 'reg::character';
$filter->block->default->get['param']           = 'reg::base64';
$filter->entry->visit->get['referer']           = 'reg::base64';
$filter->entry->depts->get['key']               = 'reg::key';
$filter->entry->users->get['key']               = 'reg::key';
$filter->file->ajaxueditorupload->get['action'] = 'equal::config';
$filter->file->filemanager->get['path']         = 'reg::path';
$filter->file->filemanager->get['order']        = 'reg::common';

$filter->sso->check->get['callback']            = 'reg::callback';
$filter->sso->check->get['referer']             = 'reg::base64';
$filter->sso->check->get['token']               = 'reg::key';
$filter->sso->check->get['auth']                = 'reg::key';
$filter->sso->check->get['userIP']              = 'ip';
$filter->upgrade->upgradelicense->get['agree']  = 'equal::true';
$filter->user->login->get['lang']               = 'reg::lang';
$filter->rss->index->get['type']                = 'reg::common';

$filter->trade->browse->cookie['treeview']           = 'code';
$filter->doc->browse->cookie['browseType']           = 'reg::browseType';
$filter->project->index->cookie['taskListType']      = 'code';
$filter->file->download->cookie[$config->sessionVar] = 'reg::common';
$filter->my->project->cookie['taskListType']         = 'code';
$filter->todo->calendar->cookie['todoCalendarSide']  = 'equal::hide';
$filter->webapp->obtain->cookie['pagerWebappObtain'] = 'int';

$filter->project->default->cookie['lastProject'] = 'int';
$filter->project->default->cookie['projectMode'] = 'code';
$filter->reply->default->cookie['r']             = 'reg::checked';
$filter->thread->default->cookie['t']            = 'reg::checked';
