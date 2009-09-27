<?php
require('./includes/settings.php');
require('./includes/session.php');
try {
		$service_string = "{" . IMAP_HOST . ":" 
				. IMAP_PORT . "" . IMAP_SERVICE . "}" . IMAP_FOLDER;
		
		$mbox = @imap_open($service_string,
				 $_SESSION['email'], 
				 $_SESSION['password'])
				 or 
				 die(imap_last_error()."<br/>Connection Faliure!");
} catch (Exception $e) {
	error_log($e);
}
$msg_number = imap_msgno($mbox, $_GET['message_uid']);
if(!is_int($msg_number)) {
	die('Error: Message identifier is not a number');
}
if(isset($_GET['page']) && is_numeric($_GET['page'])) {
	$page = $_GET['page'];
} else {
	$page = 0;
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
$body = strtr(strip_tags(iconv($charset, 'UTF-8', imap_fetchbody($mbox, $msg_number, '1'))), array("\r\n" => "<br/>", "&" => "&amp;", "<" => "&lt;", ">" => "&gt;", "\"" => "&quot;", "'" => "&#039;"));


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
				<load-page event="activate" page="home.bp?srcp=message"/>
			</tab>
			<tab id='write'>
				<label>Compose</label>
				<load-page event="activate" page="compose.bp?srcp=message"/>
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
			 <label>Inbox</label>
			 <load-page event="activate" page="index.php?page=<?php echo($page); ?>" />
		 </back>
	 </navigation-bar>
	</page-header>
	<content>
		<module class="featured">
			<link-set>
				<inline-trigger>
					<label>Reply</label>
					<load-page use-cache="false" event="activate" page="reply.bp?f=Inbox&amp;m=AGqliGIAAQYfSry5dgMyJQBBk9c&amp;srcp=message&amp;i=0&amp;" />
				</inline-trigger>
				<inline-trigger>
					<label>Delete</label>
				<load-page use-cache="false" event="activate" page="delete.bp?f=Inbox&amp;m=AGqliGIAAQYfSry5dgMyJQBBk9c&amp;ycb=LW3R9LOhZp8&amp;srcp=message&amp;i=0" />
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
									<label><?php echo htmlentities($header->fromaddress); ?></label>
									<load event="activate" resource="widget:ygo-addressbook/contact/lookup?email=mailbot@yahoo.com" />
								</inline-trigger>
								<br/>
									<strong>To: </strong>
								<inline-trigger>
										<label><?php echo htmlentities($header->toaddress); ?></label>
										<load event="activate" resource="widget:ygo-addressbook/contact/lookup?email=palako@ymail.com" />
								</inline-trigger>
								<br/>
							</block>
						</template-item>
						<template-item field="description">
							<block>
								<span class="small subdued"><?php echo htmlentities($header->date); ?></span>
							</block>
						</template-item>
					</template-items>
				</placard>
			 </module>	
				<placard class="callout subdued" layout="simple">
					<layout-items>
						<block><strong><?php echo htmlentities($header->subject); ?></strong></block>
					</layout-items>
				</placard>	
					<block><br/><?php echo $body; ?><br/></block>
			<navigation-bar>
				<back>
					<label>Inbox</label>
					<load-page event="activate" page="index.php?page=<?php echo($page); ?>" />
				</back>
				<prev>
					<label>Older</label>
					<?php if(isset($prev_message)) { ?>
							<load-page event="activate" page="viewmessage.php?message_uid=<?php echo($prev_message . '&amp;page=' . $page); ?>"/>
					<?php } ?>	
				</prev>
				<next>
					<label>Newer</label>
					<?php if(isset($next_message)) { ?>
							<load-page event="activate" page="viewmessage.php?message_uid=<?php echo($next_message . '&amp;page=' . $page); ?>"/>
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
					<load-page event="activate" page="reply.bp?f=Inbox&amp;m=AGqliGIAAQYfSry5dgMyJQBBk9c&amp;srcp=message&amp;i=0&amp;"  accesskey="1"/>
				</placard>
				<placard layout="simple">
					<layout-items>
						<block><strong>Reply all</strong></block>			
					</layout-items>
					<load-page event="activate" page="replyall.bp?f=Inbox&amp;m=AGqliGIAAQYfSry5dgMyJQBBk9c&amp;srcp=message&amp;i=0&amp;"  accesskey="2"/>
				</placard>
				<placard layout="simple">
					<layout-items>
						<block><strong>Forward</strong></block>		
					</layout-items>
					<load-page event="activate" page="forward.bp?f=Inbox&amp;m=AGqliGIAAQYfSry5dgMyJQBBk9c&amp;srcp=message&amp;i=0&amp;" accesskey="3" />
				</placard>
				<placard layout="simple">
					<layout-items>
						<block><strong>Delete</strong></block>
					</layout-items>
					<load-page event="activate" page="delete.bp?f=Inbox&amp;m=AGqliGIAAQYfSry5dgMyJQBBk9c&amp;ycb=LW3R9LOhZp8&amp;srcp=message&amp;i=0" accesskey="4" />
				</placard>
				<placard layout="simple">
					<layout-items>
						<block><strong>Flag</strong></block>
					</layout-items>
					<load-page event="activate" page="flag.bp?f=Inbox&amp;m=AGqliGIAAQYfSry5dgMyJQBBk9c&amp;s=1&amp;ycb=LW3R9LOhZp8&amp;srcp=message" />
				</placard>
				<placard layout="simple">
					<layout-items>
						<block><strong>Move</strong></block>
					</layout-items>
					<load-page event="activate" page="select.bp?f=Inbox&amp;m=AGqliGIAAQYfSry5dgMyJQBBk9c&amp;srcp=message&amp;e=0&amp;i=0" accesskey="5" />
				</placard>
				<placard layout="simple">
					<layout-items>
						<block><strong>Mark unread</strong></block>
					</layout-items>
					<load-page event="activate" page="read.bp?f=Inbox&amp;m=AGqliGIAAQYfSry5dgMyJQBBk9c&amp;s=0&amp;ycb=LW3R9LOhZp8&amp;srcp=message&amp;i=0" />
				</placard>
				<placard layout="simple">
					<layout-items>
						<block><strong>Mark as spam</strong></block>
					</layout-items>
					<load-page event="activate" page="spam.bp?f=Inbox&amp;m=AGqliGIAAQYfSry5dgMyJQBBk9c&amp;s=1&amp;ycb=LW3R9LOhZp8&amp;srcp=message" accesskey="6" />
				</placard>
			</placard-set>
		</module>		
	</content>
</page>