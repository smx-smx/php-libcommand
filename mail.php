<?php
/*
 * Copyright Smx (smxdev4@gmail.com) 2016
 */
require_once "lnx_cmd.php";

class Mail {
	private $recipients = array();
	private $attachments = array();
	private $body = "";
	private $subject = "";

	public function __construct(){
		if(!TermCommand::cmd_exists("mutt")){
			throw new Exception("ERROR: 'mutt' is not installed on your system! cannot send mails");
		}
	}

	public function addRecipient($r){
		array_push($this->recipients, $r);
	}

	public function removeRecipient($r){
		$removed = false;
		foreach($this->getRecipients() as $i => $rec){
			if($r == $rec){
				unset($this->recipients[$i]);
				$removed = true;
			}
		}
		if($removed)
			$this->setRecipients( array_values( $this->getRecipients() ) );
		return $removed;
	}

	public function addAttachment($a){
		array_push($this->attachments, $a);
	}

	public function removeAttachment($a){
		$removed = false;
		foreach($this->getAttachments() as $i => $att){
			if($a == $att){
				unset($this->attachments[$i]);
				$removed = true;
			}
		}
		if($removed)
			$this->setAttachments( array_values( $this->getAttachments() ) );
		return $removed;
	}

	public function getAttachments(){ return $this->attachments; }
	public function setAttachments($a){ $this->attachments = $a; }
	public function getRecipients(){ return $this->recipients; }
	public function setRecipients($r){ $this->recipients = $r; }
	public function getSubject(){ return $this->subject; }
	public function setSubject($s){ $this->subject = $s; }
	public function getBody(){ return $this->body; }
	public function setBody($b){ $this->body = $b; }

	public function send(){
		if(count($this->getRecipients()) <= 0)
			return FALSE;

		$cmd = sprintf('echo "%s" | mutt -e "set content_type=text/html" -s "%s"',
			$this->getBody(), $this->getSubject());

		if(count($this->getAttachments()) > 0){
			$cmd .= " ";
			foreach($this->getAttachments() as $i => $attachment){
				$cmd .= sprintf('-a "%s"', $attachment);
			}
		}

		$cmd .= " --";
		foreach($this->getRecipients() as $i => $recipient){
			$cmd .= " ${recipient}";
		}
		$c = new TermCommand($cmd);
		return $c->execute();
	}
}
?>
