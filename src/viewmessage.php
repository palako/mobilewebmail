<?php
require('./includes/settings.php');
require('./includes/session.php');
require('./includes/util.php');
require('./includes/imapConnection.php');
require('./classes/Message.php');

$mbox = getMbox();

$message = new Message();

$msg_number = imap_msgno($mbox, $_GET['message_uid']);
if(!is_int($msg_number)) {
	die('Error: Message identifier is not a number');
}
if(isset($_GET['page']) && is_numeric($_GET['page'])) {
	$pageNumber = $_GET['page'];
} else {
	$pageNumber = 0;
}
$total_messages = imap_num_msg($mbox);  

$charset = 'UTF-8';
$structure = imap_fetchstructure($mbox, $msg_number);
foreach($structure->parameters as $param) {
	if($param->attribute == 'charset') {
		$charset = $param->value;
		break;
	}
}
$header = imap_headerinfo($mbox, $msg_number);
$message->setFrom($header->fromaddress);
$message->setTo($header->toaddress);
$message->setCc($header->ccaddress);
$message->setSubject($header->subject);
$message->setBody(imap_fetchbody($mbox, $msg_number, '1'));// '1' is the text/plain version of the body in a multipart message
$message->setDate($header->date);
$_SESSION['currentMessage'] = serialize($message);
if($msg_number > 1) {
	$prev_message=imap_uid($mbox, $msg_number-1);
} else {
	$prev_message = null;
}	
if($msg_number < $total_messages) {
	$next_message=imap_uid($mbox, $msg_number+1);
} else {
	$next_message = null;
}
imap_close($mbox);
header( "Content-Type: application/x-blueprint+xml" );
header( "Cache-Control: no-cache" );    
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<page style="list">
  <models>
    <model id="search-request">
      <instance id="search-request">
        <data>
          <query></query>       
        </data>
      </instance>
      <submission method="get" resource="search.bp"/>
    </model>
  </models>
  	<page-header>
		<page-title><?php echo ($total_messages-$msg_number+1) . ' of ' . $total_messages?></page-title>
		<tabs>
			<tab id='read'>
				<label>Messages</label>
				<load-page event="activate" page="index.php?page=<?php echo $pageNumber; ?>"/>
			</tab>
			<tab id='write'>
				<label>Compose</label>
				<load-page event="activate" page="composemessage.php"/>
			</tab>
		</tabs>
	   <navigation-bar>
		 <prev>
			 <label/>
		 </prev>
		 <next>
			 <label/>		 
		</next>
		 <back>
			 <label><?php echo ucfirst(getCurrentFolder()); ?></label>
			 <load-page event="activate" page="index.php?page=<?php echo($pageNumber); ?>" />
		 </back>
	 </navigation-bar>
	</page-header>
	<content>
		<module class="featured">
			<link-set>
				<inline-trigger>
					<label>Reply</label>
					<load-page use-cache="false" event="activate" page="composemessage.php?action=reply" />
				</inline-trigger>
				<inline-trigger>
					<label>Reply all</label>
					<load-page use-cache="false" event="activate" page="composemessage.php?action=replyall" />
				</inline-trigger>
				<inline-trigger>
					<label>More...</label>
					<setfocus control="actions" event="activate"/>
				</inline-trigger>
			</link-set>
		</module> 
		<module>
			<module class="featured">
			 	<placard layout="template">
					<template-items format="title-value">
						<template-item field="title">
							<block lines="unlimited" class="subdued">
								<strong>From: </strong>
								<inline-trigger>
									<label><?php echo htmlspecialchars($message->getFrom()); ?></label>
									<load event="activate" resource="widget:ygo-addressbook/contact/lookup?email=mailbot@yahoo.com" />
								</inline-trigger>
								<br/>
								<strong>To: </strong>
								<inline-trigger>
										<label><?php echo htmlspecialchars($message->getTo()); ?></label>
										<load event="activate" resource="widget:ygo-addressbook/contact/lookup?email=palako@ymail.com" />
								</inline-trigger>
								<br/>
								<?php if($message->getCc() != '') { ?>	
								<strong>Cc: </strong>
								<inline-trigger>
										<label><?php echo htmlspecialchars($message->getCc()); ?></label>
										<load event="activate" resource="widget:ygo-addressbook/contact/lookup?email=palako@ymail.com" />
								</inline-trigger>
								<br/>
								<?php } ?>
							</block>
						</template-item>
						<template-item field="description">
							<block>
								<span class="small subdued"><?php echo htmlspecialchars($message->getDate()); ?></span>
							</block>
						</template-item>
					</template-items>
				</placard>
			 </module>	
				<placard class="callout subdued" layout="simple">
					<layout-items>
						<block><strong><?php echo htmlspecialchars(decodeMimeStr($message->getSubject())); ?></strong></block>
					</layout-items>
				</placard>	
					<block><br/><?php echo htmlspecialchars($message->getBody()); ?><br/></block>
			<navigation-bar>
				<back>
					<label>Inbox</label>
					<load-page event="activate" page="index.php?page=<?php echo($pageNumber); ?>" />
				</back>
				<prev>
					<label>Older</label>
					<?php if(isset($prev_message)) { ?>
							<load-page event="activate" page="viewmessage.php?message_uid=<?php echo($prev_message . '&amp;page=' . $pageNumber); ?>"/>
					<?php } ?>	
				</prev>
				<next>
					<label>Newer</label>
					<?php if(isset($next_message)) { ?>
							<load-page event="activate" page="viewmessage.php?message_uid=<?php echo($next_message . '&amp;page=' . $pageNumber); ?>"/>
					<?php } ?>
				</next>			
		   </navigation-bar>	
		</module>
		<module id="actions">
			<header layout="simple">
				<layout-items>
						<block class="title">Actions</block>
				</layout-items>
			</header>
			<placard-set>
				<placard layout="simple">
					<layout-items>
						<block><strong>Reply</strong></block>
					</layout-items>
					<load-page event="activate" page="composemessage.php?action=reply"  accesskey="1"/>
				</placard>
				<placard layout="simple">
					<layout-items>
						<block><strong>Reply all</strong></block>			
					</layout-items>
					<load-page event="activate" page="composemessage.php?action=replyall"  accesskey="2"/>
				</placard>
				<placard layout="simple">
					<layout-items>
						<block><strong>Forward</strong></block>		
					</layout-items>
					<load-page event="activate" page="composemessage.php?action=forward" accesskey="3" />
				</placard>
				<placard layout="simple">
					<layout-items>
						<block><strong>Delete</strong></block>
					</layout-items>
					<load-page event="activate" page="performaction.php?action=delete&amp;msgids=<?php echo($_GET['message_uid']); ?>" accesskey="4" />
				</placard>
				<placard layout="simple">
					<layout-items>
						<block><strong>Mark unread</strong></block>
					</layout-items>
					<load-page event="activate" page="performaction.php?action=unread&amp;msgids=<?php echo($_GET['message_uid']); ?>" accesskey="6" />
				</placard>
			</placard-set>
		</module>		
	</content>
</page>