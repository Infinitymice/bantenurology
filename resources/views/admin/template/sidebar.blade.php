
		<!-- Main sidebar -->
		<div class="sidebar sidebar-light sidebar-main sidebar-expand-md">

			<!-- Sidebar mobile toggler -->
			<div class="sidebar-mobile-toggler text-center">
				<a href="#" class="sidebar-mobile-main-toggle">
					<i class="icon-arrow-left8"></i>
				</a>
				Navigation
				<a href="#" class="sidebar-mobile-expand">
					<i class="icon-screen-full"></i>
					<i class="icon-screen-normal"></i>
				</a>
			</div>
			<!-- /sidebar mobile toggler -->


			<!-- Sidebar content -->
			<div class="sidebar-content">

				<!-- User menu -->
				<div class="sidebar-user">
					<div class="card-body">
						<div class="media">
							<div class="mr-3">
								<a href="#"><img src="{{ Auth::user()->profile_picture_url ?? asset('logo/user.png') }}" width="38" height="38" class="rounded-circle" alt=""></a>
							</div>

							<div class="media-body">
								<div class="media-title font-weight-semibold">{{ Auth::user()->name }}</div>
								<div class="font-size-xs opacity-50">
									<i class="icon-pin font-size-sm"></i> &nbsp;{{ Auth::user()->location ?? 'Location not set' }}
								</div>
							</div>

							<div class="ml-3 align-self-center">
								<a href="#" class="text-white"><i class="icon-cog3"></i></a>
							</div>
						</div>
					</div>
				</div>

				<!-- /user menu -->


				<!-- Main navigation -->
				<div class="card card-sidebar-mobile">
					<ul class="nav nav-sidebar" data-nav-type="accordion">

						<!-- Main -->
						<li class="nav-item-header"><div class="text-uppercase font-size-xs line-height-xs">Main</div> <i class="icon-menu" title="Main"></i></li>
						<li class="nav-item">
						<a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
							<i class="icon-home4"></i>
							<span>Dashboard</span>
						</a>
						</li>
                        <!-- <li class="nav-item">
						<a href="{{ route('admin.participants') }}" class="nav-link {{ request()->is('admin/participants') ? 'active' : '' }}">
							<i class="icon-file-css"></i>
							<span>Data Peserta</span>
						</a>
						</li> -->
						<li class="nav-item nav-item-submenu">
							<a href="#" class="nav-link"><i class="icon-file-css"></i> <span>Kelola Data</span></a>

							<ul class="nav nav-group-sub" data-submenu-title="User">
								<li class="nav-item"><a href="{{ route('admin.participants') }}" class="nav-link">Data Peserta</a></li>
								<li class="nav-item"><a href="{{ route('admin.payments') }}" class="nav-link">Data Pembayaran</a></li>
								<li class="nav-item"><a href="{{ route('admin.invoices') }}" class="nav-link">Data Invoice</a></li>
							</ul>
						</li>
						<li class="nav-item nav-item-submenu">
							<a href="#" class="nav-link"><i class="icon-color-sampler"></i> <span>Kelola User</span></a>

							<ul class="nav nav-group-sub" data-submenu-title="User">
								<li class="nav-item"><a href="{{ route('admin.users.index') }}" class="nav-link">Data User</a></li>
							</ul>
						</li>
						<li class="nav-item nav-item-submenu">
							<a href="#" class="nav-link"><i class="icon-copy"></i> <span>Kelola Events</span></a>
							<ul class="nav nav-group-sub" data-submenu-title="Data Events">
								<li class="nav-item"><a href="{{ route('admin.event-types.index') }}" class="nav-link">Event Types</a></li>
								<li class="nav-item"><a href="{{ route('admin.index') }}" class="nav-link">Event</a></li>
							</ul>
						</li>
						<li class="nav-item nav-item-submenu">
							<a href="#" class="nav-link"><i class="fa fa-hotel"></i> <span>Kelola Akomodasi</span></a>
							<ul class="nav nav-group-sub" data-submenu-title="Akomodasi">
								<li class="nav-item"><a href="{{ route('admin.accommodation.index') }}" class="nav-link">Data Akomodasi</a></li>
							</ul>
						</li>
						<li class="nav-item nav-item-submenu">
							<a href="#" class="nav-link"><i class="fa fa-hotel"></i> <span>Kelola Group</span></a>
							<ul class="nav nav-group-sub" data-submenu-title="Akomodasi">
								<li class="nav-item"><a href="{{ route('admin.group-codes.index') }}" class="nav-link">Data Group</a></li>
							</ul>
						</li>

						<!-- /main -->
 
						<!-- Forms -->
						<li class="nav-item-header"><div class="text-uppercase font-size-xs line-height-xs">Forms</div> <i class="icon-menu" title="Forms"></i></li>
						<li class="nav-item nav-item-submenu">
							<a href="#" class="nav-link"><i class="icon-pencil3"></i> <span>Kelola Absensi</span></a>
							<ul class="nav nav-group-sub" data-submenu-title="Form components">
								<!-- <li class="nav-item"><a href="{{ route('admin.reset.absensi') }}" class="nav-link">Reset Absensi</a></li> -->
								<li class="nav-item"><a href="{{ route('admin.settings.index') }}" class="nav-link">Setting Absensi</a></li>
								<li class="nav-item"><a href="{{ route('admin.absensi.index') }}" class="nav-link">Data Absensi</a></li>
								
							</ul>
						</li>
						<li class="nav-item nav-item-submenu">
							<a href="#" class="nav-link"><i class="icon-qrcode"></i> <span>Kelola Qr Code</span></a>
							<ul class="nav nav-group-sub" data-submenu-title="Form components">
								<!-- <li class="nav-item"><a href="{{ route('admin.reset.absensi') }}" class="nav-link">Reset Absensi</a></li> -->
								<li class="nav-item"><a href="{{ route('admin.download.index') }}" class="nav-link">Data Qr Code</a></li>
							</ul>
						</li>
						<li class="nav-item nav-item-submenu">
							<a href="#" class="nav-link"><i class="icon-spinner spin"></i> <span>Wheels Of Name</span></a>
							<ul class="nav nav-group-sub" data-submenu-title="Form components">
								<li class="nav-item"><a href="{{ route('admin.setting-wheel') }}" class="nav-link">Setting Wheels</a></li>
								<li class="nav-item"><a href="{{ route('admin.wheel.index') }}" class="nav-link">Wheels</a></li>
							</ul>
						</li>
						<!-- /forms -->
					</ul>
				</div>
				<!-- /main navigation -->

			</div>
			<!-- /sidebar content -->
			
		</div>

        