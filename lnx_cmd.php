<?php
/*
 * Copyright Smx (smxdev4@gmail.com) 2016
 */
class TermCommand {
	private $command;
	private $output;
	private $exitcode;
	private $handle;

	public static function cmd_exists($cmd){
		$check = sprintf("which %s &>/dev/null", $cmd);
		exec($check, $result, $retcode);
		return ($retcode == 0) ? true : false;
	}

	public static function commands_exist($commands, $verbose=true){
		$ret = true;
		foreach($commands as $cmd){
			if(TermCommand::cmd_exists($cmd)){
				if($verbose)
					printf("[+] \"%s\" => OK\n", $cmd);
			} else {
				printf("[-] \"%s\" => MISSING\n", $cmd);
				$ret = false;
			}
		}
		return $ret;
	}

	public static function getExitCode($status){
		return pcntl_wexitstatus($status);
	}

	public static function destroy($h){
		$status = pclose($h);
		return self::getExitCode($status);
	}

	public function __construct($cmd=null){
		$this->setCommand($cmd);
	}

	public function getCommand(){ return $this->command; }
	public function setCommand($cmd){ $this->command = $cmd; }

	private function setHandle($h){ $this->handle = $h; }
	public function getHandle(){ return $this->handle; }

	public function execute(){
		if(!$this->getCommand()){
			return FALSE;
		}
		$h = popen($this->getCommand(), "r");
		if(!$h){
			return FALSE;
		}

		$this->setHandle($h);
		return $this->getHandle();
	}
}
?>
