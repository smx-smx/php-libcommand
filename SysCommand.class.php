<?php
/*
 * Copyright Smx (smxdev4@gmail.com) 2016
 */

require_once "Log.class.php";
require_once "TermCommand.class.php";

class SysCommand {
	private $deps = array();
	private $cmdfunc = null;
	private $log = null;
	private $depsChecked = false;

	public function __construct($log=null){ $this->log = $log; }

	public function getDeps(){ return $this->deps; }
	public function setDeps($cmds){ $this->deps = $cmds; }


	public function getCommandFunc(){ return $this->cmdfunc; }
	public function setCommandFunc($func){ $this->cmdfunc = $func; }

	public function executeFunc($args=array()){
		if(!$this->depsChecked){
			if(TermCommand::commands_exist($this->getDeps()) === FALSE){
				return FALSE;
			}
			$this->depsChecked = true;
		}
		return call_user_func_array($this->getCommandFunc(), array());
	}

	public function getCommandString(){ return $this->cmd; }
	public function setCommandString($cmd){ $this->cmd = $cmd; }

	public function executeCmd($cmd=null){
		if(is_null($cmd)){
			$cmd = $this->getCommandString();
		}
		if(!is_null($this->log) && $this->log->isLogStderrEnabled()){
			$cmd .= " 2>&1";
		}
		if(!$this->depsChecked){
			if(TermCommand::commands_exist($this->getDeps()) === FALSE){
				return FALSE;
			}
			$this->depsChecked = true;
		}
		$c = new TermCommand($cmd);
		return $c->execute();
	}
}
?>
