<?php
/*
 * Copyright Smx (smxdev4@gmail.com) 2016
 */
require_once "log.php";
require_once "scommand.php";
require_once "mail.php";

$l = new LogFile(array(
	Log::LOG_BASENAME => "smxtest",
	Log::LOG_DIR => ".",
	Log::LOG_EXT => "log",
	Log::LOG_QUIET => false
));
if($l->initLog() === FALSE){
	fprintf(STDERR, "ERROR: Cannot create log file!\n");
	return 1;
}

$c = new SCommand();
$c->setDeps(array("ls"));
$h = $c->executeCmd("ls");
while(!feof($h)){
	$line = fgets($h);
	if($line !== FALSE){
		$l->add($line);
	}
}
$code = TermCommand::destroy($h);

$m = new Mail();
$m->setRecipients(array(
	"user@example.com"
));
$m->addAttachment($l->getLogFinalName());
$m->setSubject("Test");
$m->setBody("<html><h1>Hello World!<h1></html>");
$m->send();
return 0;
?>
