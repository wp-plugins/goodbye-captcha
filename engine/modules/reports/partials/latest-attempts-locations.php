<script>
	var attemptsCountryArray = {<?php echo $latestAttemptsLocationJs ?>};
</script>
	<article class="col-sm-6">
		<div id="wid-id-2" class="gdbcwidget clearfix">
			<header>
				<span class="widget-icon icon-primary"><span class="glyphicon glyphicon-map-marker"></span></span>
				<h2>Attempts Top Locations</h2>
			</header>
			<div class="no-padding">
				<div class="widget-body" class="tab-content">
					<div class="padding-10">
						<div class="row no-space">
							<div id="vector-map" class="vector-map">
							</div>
							<table class="table countriesTable table-hover">
								<thead>
									<tr>
										<?php foreach($latestAttemptsLocationHeader as $item) {?>
											<th> <?php echo $item; ?> </th>
										<?php } ?>
									</tr>
								</thead>
								<tbody>
									<?php if (!isset($latestAttemptsLocationArray[0])) { ?>
										<tr>
											<td colspan="<?php echo count($latestAttemptsLocationHeader); ?>" style="text-align: center !important"> No records found </td>
										</tr>
									<?php } else {
										for($i = 0, $arrSize = count($latestAttemptsLocationArray); $i < $arrSize; ++$i)
										{ ?>
										<tr>
											<?php foreach($latestAttemptsLocationHeader as $itemKey => $itemValue) { ?>
												<td> <?php echo $latestAttemptsLocationArray[$i][$itemKey]; ?> </td>
											<?php } ?>
										</tr>
									<?php }//end for
									}//end else ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</article>
