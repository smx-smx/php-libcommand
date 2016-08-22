#!/usr/bin/env php
<?php
require_once "libcommand.php";

$l = new LogFile(array(
	Log::LOG_BASENAME => "apt_mon",
	Log::LOG_DIR => "/var/log/smx/",
	Log::LOG_EXT => "log",
	Log::LOG_QUIET => false //print log on stdout
));

if($l->initLog() === FALSE){
	fprintf(STDERR, "ERROR: Cannot create log file!\n");
	return 1;
}

$c = new SysCommand($l);
$c->setDeps(array("aptitude"));

$h = $c->executeCmd("aptitude update");
while(!feof($h)){
	$line = fgets($h);
	if($line !== FALSE){
		$l->add($line);
	}
}
$code = TermCommand::destroy($h);
if($code != 0){
	$l->add("[-] aptitude update failed!\n");
	goto sendLog;
}

$h = $c->executeCmd('aptitude -F"%p|-|%v|-|%V" --disable-columns search ~U');
$l->add("[+] Available updates:\n\n");
while(!feof($h)){
	$line = fgets($h);
	if($line === FALSE)
		continue;

	$fields = explode("|-|", trim($line));
	if(count($fields) !== 3)
		continue;
	if($fields[1] == $fields[2])
		continue;
	$l->add("%-35s %-30s %-30s\n", $fields[0], $fields[1], $fields[2]);
}

sendLog:
$m = new Mail();
$m->setRecipients(array(
	"user@example.com"
));

$m->addAttachment($l->getLogFinalName());
$m->setSubject("Software Updates Available!");

$body="
<h2>Hello server administrator</h2>
<p>New software updates are available</p>
<p>For details, look at the attached log file</p>
";

$m->setBody($body);
$m->send();
return 0;
?>
