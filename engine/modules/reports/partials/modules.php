<div class="row">
	<article class="col-sm-12">
		<div id="wid-id-1000" class="gdbcwidget clearfix">
			<header>
				<span class="widget-icon icon-primary"><span class="glyphicon glyphicon-dashboard"></span></span>

				<h2>Dashboard</h2>
				<ul id="dashboard-navigation" class="nav nav-tabs pull-right in">
					<li>
						<a href="<?php echo $statsPageUrl; ?>">
							<i class="glyphicon glyphicon-stats"></i>
							<span class="hidden-mobile hidden-tablet">Stats</span>
						</a>
					</li>
					<li class="active">
						<a data-toggle="tab">
							<i class="glyphicon glyphicon-list-alt"></i>
							<span class="hidden-mobile hidden-tablet">Modules</span>
						</a>
					</li>
				</ul>
			</header>
			<div class="no-padding">
				<div class="widget-body" class="tab-content">
					<div id="modules-chart" class="toolbar">
						<div class="inline-group">
						</div>
					</div>
					<div class="padding-10">
						<div id="flot-container">

						</div>
					</div>
				</div>
			</div>
		</div>
	</article>
</div>