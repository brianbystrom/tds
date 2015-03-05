<?

// Template Functions go here.
/*
Template Function List
page_header($page); 
page_body_index();
page_footer();
*/

	function site_header($name,$cid) {
		
?>

	<!-- BEGIN HEADER TOP -->
	<div class="page-header-top">
		<div class="container">
			<!-- BEGIN LOGO -->
			<div class="page-logo">
				<a href="index.html"><img src="../../assets/admin/layout3/img/logo-blue-steel.png" alt="logo" class="logo-default"></a>
			</div>
			<!-- END LOGO -->
			<!-- BEGIN RESPONSIVE MENU TOGGLER -->
			<a href="javascript:;" class="menu-toggler"></a>
			<!-- END RESPONSIVE MENU TOGGLER -->
			<!-- BEGIN TOP NAVIGATION MENU -->
			<div class="top-menu">
				<ul class="nav navbar-nav pull-right">
					
					<li class="droddown dropdown-separator">
						<span class="separator"></span>
					</li>
					
					<!-- BEGIN USER LOGIN DROPDOWN -->
					<li class="dropdown dropdown-user dropdown-dark">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<img alt="" class="img-circle" src="../../assets/admin/layout3/img/avatar9.jpg">
						<span class="username username-hide-mobile"><? echo $name.' ('.$cid.')'; ?></span>
						</a>
						<ul class="dropdown-menu dropdown-menu-default">
							<li>
								<a href="extra_profile.html">
								<i class="icon-user"></i> My Profile </a>
							</li>
							<li>
								<a href="page_calendar.html">
								<i class="icon-calendar"></i> My Calendar </a>
							</li>
							<li>
								<a href="inbox.html">
								<i class="icon-envelope-open"></i> My Inbox <span class="badge badge-danger">
								3 </span>
								</a>
							</li>
							<li>
								<a href="javascript:;">
								<i class="icon-rocket"></i> My Tasks <span class="badge badge-success">
								7 </span>
								</a>
							</li>
							<li class="divider">
							</li>
							<li>
								<a href="extra_lock.html">
								<i class="icon-lock"></i> Lock Screen </a>
							</li>
							<li>
								<a href="login.html">
								<i class="icon-key"></i> Log Out </a>
							</li>
						</ul>
					</li>
					<!-- END USER LOGIN DROPDOWN -->
				</ul>
			</div>
			<!-- END TOP NAVIGATION MENU -->
		</div>
	</div>
	<!-- END HEADER TOP -->

<? 
	
	} 

	function page_nav() {
?>

<!-- BEGIN HEADER MENU -->
	<div class="page-header-menu">
		<div class="container">
			<!-- BEGIN HEADER SEARCH BOX -->
			<form class="search-form" action="extra_search.html" method="GET">
				<div class="input-group">
					<input type="text" class="form-control" placeholder="Search" name="query">
					<span class="input-group-btn">
					<a href="javascript:;" class="btn submit"><i class="icon-magnifier"></i></a>
					</span>
				</div>
			</form>
			<!-- END HEADER SEARCH BOX -->
			<!-- BEGIN MEGA MENU -->
			<!-- DOC: Apply "hor-menu-light" class after the "hor-menu" class below to have a horizontal menu with white background -->
			<!-- DOC: Remove data-hover="dropdown" and data-close-others="true" attributes below to disable the dropdown opening on mouse hover -->
			<div class="hor-menu ">
				<ul class="nav navbar-nav">
					<li>
						<a href="index.php">Home</a>
					</li>
					
					<li class="menu-dropdown classic-menu-dropdown ">
						<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
						Program <i class="fa fa-angle-down"></i>
						</a>
						<ul class="dropdown-menu pull-left">
							<li class=" dropdown-submenu">
								<a href=":;">
								<i class="icon-globe"></i>
								AT&T </a>
								<ul class="dropdown-menu pull-left">
									<li class=" dropdown-submenu ">
										<a href="#">
										Android </a>
										<ul class="dropdown-menu">
											<li class=" ">
												<a href="#">
												Team Analyzer </a>
											</li>
											<li class=" ">
												<a href="#">
												Attainment <span class="badge badge-roundless badge-danger">new</span>
												</a>
											</li>
										</ul>
									</li>
									<li class=" dropdown-submenu ">
										<a href="#">
										BCS </a>
										<ul class="dropdown-menu">
											<li class=" ">
												<a href="#">
												Team Analyzer </a>
											</li>
											<li class=" ">
												<a href="#">
												Attainment <span class="badge badge-roundless badge-danger">new</span>
												</a>
											</li>
										</ul>
									</li>
									<li class=" dropdown-submenu ">
										<a href="#">
										Billing & Accounts </a>
										<ul class="dropdown-menu">
											<li class=" ">
												<a href="#">
												Team Analyzer </a>
											</li>
											<li class=" ">
												<a href="#">
												Attainment <span class="badge badge-roundless badge-danger">new</span>
												</a>
											</li>
										</ul>
									</li>
									<li class=" dropdown-submenu ">
										<a href="#">
										Mobility Sales Chat </a>
										<ul class="dropdown-menu">
											<li class=" ">
												<a href="#">
												Team Analyzer </a>
											</li>
											<li class=" ">
												<a href="#">
												Attainment <span class="badge badge-roundless badge-danger">new</span>
												</a>
											</li>
										</ul>
									</li>
								</ul>
							</li>
						</ul>
					</li>
				</ul>
			</div>
			<!-- END MEGA MENU -->
		</div>
	</div>
	<!-- END HEADER MENU -->

<?  
	}

	function page_header($page) {

?>

<!-- BEGIN PAGE HEAD -->
	<div class="page-head">
		<div class="container">
			<!-- BEGIN PAGE TITLE -->
			<div class="page-title">
				<h1><? echo $page; ?><small><? echo ' '.$page; ?></small></h1>
			</div>
			<!-- END PAGE TITLE -->
			<!-- BEGIN PAGE TOOLBAR -->
			<div class="page-toolbar">
				
			</div>
			<!-- END PAGE TOOLBAR -->
		</div>
	</div>
	<!-- END PAGE HEAD -->


<?
	}

	function site_footer() {

?>

	<!-- BEGIN PRE-FOOTER -->
		<div class="page-prefooter">
			<div class="container">
				<div class="row">
					<div class="col-md-3 col-sm-6 col-xs-12 footer-block">
						<h2>About</h2>
						<p>
							 QI Tools provide an intuitive way to identify trending and focus areas for your team.
						</p>
					</div>
					<div class="col-md-3 col-sm-6 col-xs12 footer-block">
						<h2>Subscribe Email</h2>
						<div class="subscribe-form">
							<form action="javascript:;">
								<div class="input-group">
									<input type="text" placeholder="mail@email.com" class="form-control">
									<span class="input-group-btn">
									<button class="btn" type="submit">Submit</button>
									</span>
								</div>
							</form>
						</div>
					</div>
					<div class="col-md-3 col-sm-6 col-xs-12 footer-block">
						<h2>Follow Us On</h2>
						<ul class="social-icons">
							<li>
								<a href="javascript:;" data-original-title="rss" class="rss"></a>
							</li>
							<li>
								<a href="javascript:;" data-original-title="facebook" class="facebook"></a>
							</li>
							<li>
								<a href="javascript:;" data-original-title="twitter" class="twitter"></a>
							</li>
							<li>
								<a href="javascript:;" data-original-title="googleplus" class="googleplus"></a>
							</li>
							<li>
								<a href="javascript:;" data-original-title="linkedin" class="linkedin"></a>
							</li>
							<li>
								<a href="javascript:;" data-original-title="youtube" class="youtube"></a>
							</li>
							<li>
								<a href="javascript:;" data-original-title="vimeo" class="vimeo"></a>
							</li>
						</ul>
					</div>
					<div class="col-md-3 col-sm-6 col-xs-12 footer-block">
						<h2>Contacts</h2>
						<address class="margin-bottom-40">
						Phone: 800 123 3456<br>
						 Email: <a href="mailto:brian.bystrom@sykes.com">Brian.Bystrom@SYKES.com</a>
						</address>
					</div>
				</div>
			</div>
		</div>
		<!-- END PRE-FOOTER -->
		<!-- BEGIN FOOTER -->
		<div class="page-footer">
			<div class="container">
				 2014 &copy; QITools. All Rights Reserved.
			</div>
		</div>
		<div class="scroll-to-top">
			<i class="icon-arrow-up"></i>
		</div>
	<!-- END FOOTER -->

<?
	}
?>
