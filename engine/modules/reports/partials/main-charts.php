<div class="row">
	<article class="col-sm-12">
		<div id="wid-id-0" class="gdbcwidget clearfix">
			<header>
				<span class="widget-icon icon-primary"><span class="glyphicon glyphicon-dashboard"></span></span>

				<h2>Dashboard</h2>
				<ul id="dashboard-navigation" class="nav nav-tabs pull-right in">
					<li class="active">
						<a>
							<i class="glyphicon glyphicon-stats"></i>
							<span class="hidden-mobile hidden-tablet">Stats</span>
						</a>
					</li>
					<li>
						<a href="<?php echo $modulesPageUrl; ?>">
							<i class="glyphicon glyphicon-list-alt"></i>
							<span class="hidden-mobile hidden-tablet">Modules</span>
						</a>
					</li>
				</ul>
			</header>
			<div class="no-padding">
				<div class="widget-body" class="tab-content">
					<div class="tab-pane fade active in padding-10 no-padding-bottom" id="s1">
						<div class="row no-space">
							<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
								<div id="chart-container">
								</div>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
								<div class="row">
									<?php
										$progressBarClassArray = array(
											'bg-color-dark-blue',
											'bg-color-dark-orange',
											'bg-color-blue',
											'bg-color-greenLight',
										);
									?>
									<?php
										if (!isset($wpSectionsPercentageArray[0]))
										{
											$wpSectionsPercentageArray[0] = array('undefined 1' => 0, 'undefined 2' => 0, 'undefined 3' => 0, 'undefined 4' => 0);
										}
										$i = 0;
										foreach($wpSectionsPercentageArray[0] as $sectionKey => $sectionValue)
										{
									?>
										<div class="col-xs-6 col-sm-6 col-md-12 col-lg-12">
											<span class="text">
												<?php echo $sectionKey; ?>
												<span class="pull-right">
													<?php echo $sectionValue; ?>%
													<?php
														$lastMonthAttempt = '';
														if (isset($wpSectionsPercentageArray[1][$sectionKey]))
															$lastMonthAttempt = $wpSectionsPercentageArray[1][$sectionKey];
														$previousMonthAttempt = '';
														if (isset($wpSectionsPercentageArray[2][$sectionKey]))
															$previousMonthAttempt = $wpSectionsPercentageArray[2][$sectionKey];
														//$badgeTitle = '';
                                                        if ($lastMonthAttempt === '')
                                                            $lastMonthAttempt = 0;
                                                        if ($previousMonthAttempt === '')
                                                            $previousMonthAttempt = 0;
															$badgeTitle = $lastMonthAttempt . ' attempt(s) in the last month and ' . $previousMonthAttempt . ' attempt(s) in the previous month';
													?>
													<a class="badge <?php echo $progressBarClassArray[$i % 4]; ?>"
													      title="<?php echo $badgeTitle; ?>">i</a>
												</span>
											</span>
											<div class="progress">
												<?php $progress = $sectionValue > 100 ? 100 : $sectionValue; ?>
												<div class="progress-bar <?php echo $progressBarClassArray[$i % 4]; ?>" style="width: <?php echo $progress == 0 ? 1 : $progress; ?>%"></div>
											</div>
										</div>
									<?php $i++;} ?>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</article>
</div>