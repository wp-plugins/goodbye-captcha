<p>Hi <?php echo $adminFullName; ?>,</p>
<p>GoodBye Captcha was set to <i>Test Mode</i> in your website and received the following request information:</p>

<div style = "padding:10px 0 0;color:#333;">
	<span style="color:#999; width:125px; display:inline-block;">Submitted Form:</span>
	<span style="font-weight:bold"><?php echo $submittedForm; ?></span>
</div>

<div style = "padding:10px 0 0;color:#333;">
	<span style="color:#999; width:125px; display:inline-block;">Test Status:</span>
	<span style="font-weight:bold"><?php echo empty($rejectReason) ? 'Pass' : 'Rejected';?></span>
</div>

<?php if(!empty($rejectReason)){ ?>

<div style = "padding:10px 0 0;color:#333;">
	<span style="color:#999; width:125px; display:inline-block;">Reject Reason:</span>
	<span style="font-weight:bold"><?php echo $rejectReason; ?></span>
</div>

<?php } ?>

<div style = "padding:10px 0 0;color:#333;">
	<span style="color:#999; width:125px; display:inline-block;">Client Ip Address:</span>
	<span style="font-weight:bold"><?php echo MchHttpRequest::getClientIp(array());?></span>
</div>

<div style = "padding:10px 0 0;color:#333;">
	<span style="color:#999; width:125px; display:inline-block;">Client User Agent:</span>
	<span style="font-weight:bold"><?php echo $_SERVER['HTTP_USER_AGENT'];?></span>
</div>


<div style = "padding:10px 0 0">

	<?php if(!empty($rejectReason)){ ?>
		<p style="font-weight:bold; text-align:center; color:#81040B;">Warning : Do not activate protection for <?php echo $submittedForm . ' !'; ?></p>
	<?php } ?>

	<?php if(empty($rejectReason)){ ?>
		<p style="font-weight:bold; text-align:center; color:#208115" >Feel free to activate protection for <?php echo $submittedForm . ' !'; ?></p>
	<?php } ?>

</div>

