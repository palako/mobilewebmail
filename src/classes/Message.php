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
	
	public function getFrom() {
		return decodeMimeStr($this->from);
	}
	
	public function setFrom($from) {
		$this->from = $from;
	}
	
	public function getTo() {
		return decodeMimeStr($this->to);
	}
	
	public function setTo($to) {
		$this->to = $to;
	}
	
	public function getCc() {
		return decodeMimeStr($this->cc);
	}
	
	public function setCc($cc) {
		$this->cc = $cc;
	}
	
	public function getBcc() {
		return decodeMimeStr($this->bcc);
	}
	
	public function setBcc($bcc) {
		$this->bcc = $bcc;
	}
	
	public function getBody() {
		return decodeMimeStr(strtr(strip_tags(iconv($charset, 'UTF-8', $this->body)), array("\r\n" => "<br/>", "&" => "&amp;", "<" => "&lt;", ">" => "&gt;", "\"" => "&quot;", "'" => "&#039;")));
	}
	
	public function setBody($body) {
		$this->body = $body;
	}
	
	public function setDate($date) {
		$this->date = $date;
	}
	
	public function getDate() {
		return decodeMimeStr($this->date);
	}
}