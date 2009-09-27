<?php

	require('./includes/settings.php');
	require('./includes/session.php');
	require('./includes/imapConnection.php');
	

	header("Content-Type: application/x-blueprint+xml");
	header( "Cache-Control: no-cache" );
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	
	
	$mbox = getMbox();
	$currentMbox = getCurrentFolder();
	if(isset($_GET['mbox'])) {
		$currentMbox = $_GET['mbox'];
	}
	
	$folders = explode('.', $currentMbox);
	array_pop($folders);
	if(count($folders) > 0) {
		$back_folder = implode('.', $folders);
	}
	
	$mail_boxes = imap_getsubscribed($mbox, getServiceString(), '%');
	if(empty($mail_boxes)) {
		$mail_boxes = array();
	}
	
	$mbox_info = imap_status($mbox, getServiceString(), SA_ALL);
	$num_messages = 0 + $mbox_info->messages;
	$unread_messages = 0 + $mbox_info->unseen;

?>
<page style="list">
  <models>
    <model id="search-request">
      <instance id="search-request">
        <data>
          <query></query>
           <bkurl>folders.bp?srcp=messagelist&amp;_ts=1253931025&amp;.intl=us&amp;.lang=en</bkurl>

          <srcp>folders</srcp>
        </data>
      </instance>
     <submission method="urlencoded-post" resource="search.bp"/>
    </model>
  </models>
  <page-header>
		<page-title><?php echo $_SESSION['email']; ?></page-title>

		<tabs>
			<tab id='read'>
				<label>Messages</label>
				<load-page event="activate" page="index.php"/>
			</tab>
			<tab id='write'>
				<label>Compose</label>
				<load-page event="activate" page="compose.bp?srcp=folders"/>

			</tab>
		</tabs>
		<navigation-bar>
		<?php
		
			if(isset($back_folder)) {
		?>
		<back>
			     <label><?php echo $back_folder; ?></label>
				<load event='activate' resource="folders.php?mbox=<?php echo $back_folder; ?>"/>
			</back>
		<?php
			}
		 ?>
		 </navigation-bar>
	</page-header>
	<content>
			   		<module>			 
			 <search-box ref='query' model='search-request'>
			 	<hint>Search Mail</hint>

			 	<label>Search</label>
			 </search-box>
		</module>
		
		<module class="featured">
		<placard class="callout" layout="card">
					<layout-items>
						<block class="title">

							<strong><?php echo $currentMbox;?></strong>
						</block>
												<block class="small"><?php echo "($num_messages messages / $unread_messages unread )"; ?></block>					
					</layout-items>
					<load-page event="activate" page="setfolder.php?mbox=<?echo $currentMbox; ?>"/>
				</placard>
        </module>
        <module>
		<?php
			foreach($mail_boxes as $mail_box) {
				$name = substr(str_replace(getServiceString(), '', 
								$mail_box->name), 1);
		?>
							<placard layout="simple" class="link">
					<layout-items>
							<block><strong><?php echo $name; ?></strong></block>

							
						</layout-items>
						
					
										<load-page event="activate" 
					page="folders.php?mbox=<?php echo "$currentMbox.$name"; ?>"/>
										
				</placard>
		<?php
			}
		?>
					</module>

			

	</content>
</page> 