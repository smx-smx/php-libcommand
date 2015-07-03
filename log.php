<?php
/*
 * Copyright Smx (smxdev4@gmail.com) 2016
 */
require_once "common.php";
abstract class Log {
	const LOG_BASENAME = 0;
	const LOG_DIR = 1;
	const LOG_EXT = 2;
	const LOG_GENNAME = 3;
	const LOG_OVERWRITE = 4;
	const LOG_DEBUG = 5;
	const LOG_STDERR = 6;
	const LOG_QUIET = 7;
	const LOG_VERBOSE = 8;
}
class LogFile {
	private $debug = false;
	private $quiet = false;

	private $log_dir = "/var/log";
	private $log_name = "my_log";
	private $log_ext = "log";
	private $log_overwrite = false;
	private $log_stderr = false;
	private $log_gen_nameCB = "LogFile::genLogName";

	private $log_file = null;
	private $log_final_name = null;

	public static function genLogName($log){
		$lb = $log->getLogBaseName();
		if(is_null($lb) || empty($lb)){
			throw new Exception("Log base name cannot be empty!");
		}
		$ld = $log->getLogDir();
		if(is_null($ld) || empty($ld)){
			throw new Exception("Log directory cannot be empty!");
		}

		$n = 0;
		while(True){
			$logFile = sprintf("%s/%s_%s.%d.%s",
				$ld, $lb, date("d-m-Y"), $n, $log->getLogExt());
			if(file_exists($logFile)){
				$n++;
				continue;
			} else {
				break;
			}
		}
		return $logFile;
	}

	private function debug(){
		$str = safe_sprintf(func_num_args(), func_get_args());
		if($this->isDebugEnabled()){
			$this->add($str);
		}
	}

	public function getLogBaseName(){ return $this->log_name; }
	public function setLogBaseName($val){ $this->log_name = $val; }

	public function getLogFinalName(){
		if(!is_null($this->log_final_name))
			return $this->log_final_name;
		$name = call_user_func($this->getLogGenNameCB(), $this);
		$this->log_final_name = $name;
		return $name;
	}

	public function getLogDir(){ return $this->log_dir; }
	public function setLogDir($val){ $this->log_dir = $val; }

	public function getLogExt(){ return $this->log_ext; }
	public function setLogExt($val){ $this->log_ext = $val; }

	public function isOverWriteEnabled(){ return $this->log_overwrite; }
	public function setOverWrite($val){ $this->log_overwrite = $val; }

	public function isDebugEnabled(){ return $this->debug; }
	public function setDebug($val){ $this->debug = $val; }

	public function getLogGenNameCB(){ return $this->log_gen_nameCB; }
	public function setLogGenNameCB($func){ $this->log_gen_nameCB = $func; }

	public function isLogStderrEnabled(){ return $this->log_stderr; }
	public function setLogStderr($val){ $this->log_stderr = $val; }

	public function isQuiet(){ return $this->quiet; }
	public function setQuiet($val){ $this->quiet = $val; }

	public function getFileHandle(){ return $this->log_file; }
	private function setFileHandle($val){ $this->log_file = $val; }

	public function setOptions($opts){
		foreach($opts as $k => $v){
			switch($k){
				case Log::LOG_BASENAME:
					$this->setLogBaseName($v);
					break;
				case Log::LOG_DIR:
					$this->setLogDir($v);
					break;
				case Log::LOG_EXT:
					$this->setLogExt($v);
					break;
				case Log::LOG_GENNAME:
					$this->setLogGenNameCB($v);
					break;
				case Log::LOG_OVERWRITE:
					$this->setOverWrite($v);
					break;
				case Log::LOG_DEBUG:
					$this->setDebug($v);
					break;
				case Log::LOG_STDERR:
					$this->setLogStderr($v);
					break;
				case Log::LOG_QUIET:
					$this->setQuiet($v);
					break;
				case Log::LOG_VERBOSE:
					$this->setQuiet(!$v);
					break;
			}
		}
		$lnc = $this->getLogGenNameCB();
		if(is_null($lnc) || !is_callable($lnc)){
			throw new Exception("The specified name callback is invalid!");
		}
	}

	public function __construct($options=null){
		if(is_array($options)){
			$this->setOptions($options);
		}
	}
	public function add(){
		$str = safe_sprintf(func_num_args(), func_get_args());
		if(!$this->isQuiet()){
			print($str);
		}
		if($this->getFileHandle())
			fwrite($this->getFileHandle(), $str);

	}
	public function initLog(){
		$logFile = $this->getLogFinalName();
		$this->debug("[+] Creating log file %s\n", $logFile);
		$log = fopen($logFile, "w");
		if(!$log){
			/*fprintf(STDERR, "[-] Cannot create log file!\n");
			throw new Exception("Cannot create log file!");*/
			return FALSE;
		}
		$this->setFileHandle($log);
		return TRUE;
	}
	public function deinitLog(){
		$h = $this->getFileHandle();
		fclose($h);
	}
}
?>
