<?php
class Message {

	private $subject='';
	private $from='';
	private $to='';
	private $cc='';
	private $bcc='';
	private $body='';
	private $date='';
	
	public function getSubject() {
		return $this->subject;
	}
	
	public function setSubject($subject) {
		$this->subject = $subject;
	}
	
	public function getFrom($raw=false) {
		
		return $raw?$this->from:strtr(decodeMimeStr($this->from), array("&" => "&amp;"));
	}
		
	public function setFrom($from) {
		$this->from = $from;
	}
	
	public function getTo($raw=false) {
		return $raw?$this->to:strtr(decodeMimeStr($this->to), array("&" => "&amp;"));
	}
	
	public function setTo($to) {
		$this->to = $to;
	}
	
	public function getCc($raw=false) {
		return $raw?$this->cc:strtr(decodeMimeStr($this->cc), array("&" => "&amp;"));
	}
	
	public function setCc($cc) {
		$this->cc = $cc;
	}
	
	public function getBcc($raw=false) {
		return $raw?$this->bcc:strtr(decodeMimeStr($this->bcc), array("&" => "&amp;"));
	}
	
	public function setBcc($bcc) {
		$this->bcc = $bcc;
	}
	
	public function getBody($raw=false) {
		return $raw?$this->body:strtr(strip_tags(iconv($charset, 'UTF-8', decodeMimeStr($this->body))), array("<br/>" => "\r\n", "<br>" => "\r\n", "&" => "&amp;", "<" => "&lt;", ">" => "&gt;", "\"" => "&quot;", "'" => "&#039;"));
	}
	
	public function setBody($body) {
		$this->body = $body;
	}
	
	public function setDate($date) {
		$this->date = $date;
	}
	
	public function getDate($raw=false) {
		return $raw?$this->date:decodeMimeStr($this->date);
	}
}