<?php
$config->overtime->require = new stdclass();
$config->overtime->require->create = 'year,begin,end,type,hours';
$config->overtime->require->edit   = 'year,begin,end,type,hours';
$config->overtime->require->review = 'comment';

$config->overtime->editor = new stdclass();
$config->overtime->editor->view   = array('id' => 'lastComment', 'tools' => 'simple');
$config->overtime->editor->review = array('id' => 'comment',     'tools' => 'simple');

$config->overtime->list = new stdclass();
$config->overtime->list->exportFields = 'id, createdBy, dept, type, begin, end, hours, desc, status, reviewedBy';
