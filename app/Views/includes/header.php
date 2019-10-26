<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
	<div class="container">
		<a class="navbar-brand" href="<?=
		route_url('home', [], [App::getRequest()->getPort()])
		?>">Logo</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarNavAltMarkup">
			<div class="navbar-nav">
				<a class="nav-item nav-link<?=
				App::getRouter()->getNamedRoute('home') === current_route() ? ' active' : ''
				?>" href="<?= route_url('home', [], [App::getRequest()->getPort()]) ?>">
					<?= lang('home.home') ?> <span class="sr-only">(current)</span>
				</a>
				<a class="nav-item nav-link<?=
				App::getRouter()->getNamedRoute('contact') === current_route() ? ' active' : ''
				?>" href="<?= route_url('contact', [], [App::getRequest()->getPort()]) ?>">
					<?= lang('contact.contact') ?>
				</a>
			</div>
		</div>
	</div>
</nav>
