<?php
	require('./includes/settings.php');
	require('./includes/session.php');
	require('./includes/imapConnection.php');
	require('./includes/util.php');
	require('./classes/Message.php');

	unset($_SESSION['currentMessage']);
	
	$mbox = getMbox();	
	$imap_folder = getCurrentFolder();
	
	$current_page = intval($_GET['page']) >= 0 ? intval($_GET['page']) : 0;
	if($current_page == 0 && isset($_POST['page'])) {
		$current_page = intval($_POST['page']) >= 0 ? 
				intval($_POST['page']) : 0;	
	}
	
	$mbox_info = imap_status($mbox, getServiceString(), SA_ALL);
	
	
	$num_messages = $mbox_info->messages;
	$num_pages = round($num_messages / 10);
	$recent_messages = $mbox_info->recent;
	$unread_messages = $mbox_info->unseen;
	
	if($current_page >= $num_pages) {
		$current_page = 0;
	}

	if(isset($_GET['select_all'])) {
		$select_all = $_GET['select_all']; //sanitize this
	}
	if(isset($_GET['error_message'])) {
		$error_value = intval($_GET['error_message']);
		if($error_value >=0 && $error_value < count($error_messages)) {
			$error_message = $error_value;
		}
	}
	
header( "Content-Type: application/x-blueprint+xml" );
header( "Cache-Control: no-cache" );
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";	
?>
<page style="list">
	
    <models>
    	
        <model id="search-request">
            <instance id="search-request">
                <data>
                    <query/>
                    <bkurl>home.bp?.intl=gb&amp;.lang=en-gb</bkurl>
                    <srcp>

                        messagelist
                    </srcp>
                </data>
            </instance>
           <submission method="urlencoded-post" resource="search.bp"/>
        </model>
        <model id="messages">
            <instance>
                <manage>
                	<msgids><?php if(isset($select_all)) echo $select_all; ?></msgids>	
                    <action/>
                     <srcp>messagelist</srcp>
                    <f><?php echo $imap_folder; ?></f>
                    <page><?php echo $current_page; ?></page>
                </manage>
            </instance>

            <submission method="urlencoded-post" resource="performaction.php" />
        </model>
    </models>
    
    <page-header>
		<page-title><?php echo "$imap_folder ($unread_messages)"; ?></page-title>
		<tabs>
			<tab id='read'>
				<label>Messages</label>

				<load-page event="activate" page="index.php"/>
			</tab>
			<tab id='write'>
				<label>Compose</label>
				<load-page event="activate" page="composemessage.php"/>
			</tab>
		</tabs>
		<navigation-bar>

			<back>
			     <label>Folders</label>
				<load event='activate' resource="folders.php"/>
			</back>
						<next>
				<label>Check</label>
				<load event='activate' resource="index.php?page=0"/>
			</next>

					</navigation-bar>
	</page-header>
    <content>

	<?php
		if(isset($error_message)) {
	 ?>
	 			<placard class="callout strong" layout="card">
					<layout-items>
						<block class="description">

							<strong><?php echo $error_messages[$error_message]['title'];?></strong>
						</block>
												<block class="small"><?php echo $error_messages[$error_message]['msg']; ?></block>
											</layout-items>
				</placard>    
	 <?php
		}
	 ?>

        		<module id="search">
				<search-box ref="query" model="search-request">
					<hint>Search Mail</hint>
					<label>Search</label>

				</search-box>
		</module>
    	
		
       <module>
            
			            <select ref="msgids" model="messages" appearance="placard">
<?php 
	for ($i = 0; 
			$i < 10 && ($current_page * 10 + $i) < $num_messages; 
			$i++) {
		
		$message = new Message();
		$current_message = $num_messages - ($current_page * 10 + $i);
		$message_uid = imap_uid($mbox, $current_message);
		
		$headers = imap_headerinfo($mbox, $current_message);
		$from = $headers->from[0]->personal ? 
					$headers->from[0]->personal : $headers->from[0]->mailbox;
		$message->setFrom($from);
		$fromBlock = "<span class=\"subdued\">" . htmlentities($message->getFrom()) . "</span>";
		if($headers->Unseen == 'U') { //Not read
			$fromBlock = "<strong>" . htmlentities($message->getFrom()) . "</strong>";
		}
		
		$message->setSubject($headers->subject);
		
		$date = strtotime($headers->date);
		$now = time();
		
		$minutes_between = ($now - $date)/60;
		if($minutes_between < 60) {
			$display_date = round(($now - $date) / 60) . 'm ago';
		}
		else if ($minutes_between < 60 * 24) {
			$display_date = date('g:i a', $date);
		}
		else {
			$display_date = date('D g:i a', $date);
		}
?>
				<item>
				<placard layout="template">
					<template-items format="title-value">
						<template-item field="title">
							<block><?php echo $fromBlock; ?></block>
						</template-item>
						<template-item field="value">
							<block><?php echo $display_date; ?></block>
						</template-item>
						<template-item field="subtext" lines="1">
							<block><span class="small"><?php echo htmlentities($message->getSubject()); ?></span></block>
						</template-item>
					</template-items>
						<load event="activate"
							resource="<?php echo('viewmessage.php?message_uid=' . $message_uid . '&amp;page=' . $current_page); ?>" />
				</placard>
                  <value><?php echo $message_uid; ?></value>
                </item>
	<?php } ?>
			</select>
          <?php
          	 if($num_pages > 0) {
           ?>  
          <page-navigator>
				<page-info>
					<current-page><?php echo $current_page + 1; ?></current-page>
					<page-count><?php echo $num_pages; ?></page-count>

		        </page-info>
        	
				<prev>
					<label>Newer</label>
					<?php if($current_page > 0) { ?>
											<load-page event="activate" page="index.php?page=<?php echo $current_page - 1; ?>" accesskey="9"/>
					<?php } ?>				
									</prev>
				<next>
					<label>Older</label>
					<?php if($current_page < $num_pages - 1) { ?>
											<load-page event="activate" page="index.php?page=<?php echo $current_page + 1; ?>" accesskey="9"/>
					<?php } ?>			
									</next>

			</page-navigator>
			<?php
          	 }
			 ?>
            
            <select1 ref="action" model="messages">
                <item>
                    <label>Actions</label>
                    <value>nothing</value>
                </item>
                <item>
                    <label>Mark as read</label>

                    <value>read</value>
                </item>
                <item>
                    <label>Mark as unread</label>
                    <value>unread</value>
                </item>
                                <item>
                    <label>Delete</label>
                    <value>delete</value>
                </item>
				<item>
                    <label>Select all</label>

                    <value>selectall</value>
                </item>
                <item>
                    <label>Select none</label>
                    <value>selectnone</value>
                </item>
            </select1>
            <submit appearance="full" model="messages">
                <label>Go</label>
            </submit>
		</module>
	</content>
</page> 