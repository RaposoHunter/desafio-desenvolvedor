<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>@yield('title', config('app.name'))</title>

		<!-- Styles -->
		<link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ asset('css/style.css') }}">
		@stack('css')

		<!-- Scripts -->
		<script src="{{ asset('js/bootstrap/bootstrap.min.js') }}" defer></script>
		@stack('js')
	</head>
	<body>
		<header>
			<nav class="navbar navbar-expand-sm text-white">
				<div class="container">
					<a class="navbar-brand text-white" href="#">{{ config('app.name') }}</a>
					<button
						class="navbar-toggler d-lg-none"
						type="button"
						data-bs-toggle="collapse"
						data-bs-target="#navbar-links-container"
						aria-controls="navbar-links-container"
						aria-expanded="false"
						aria-label="Toggle navigation"
					>
						<span class="navbar-toggler-icon"></span>
					</button>
					<div class="collapse navbar-collapse" id="navbar-links-container">
						<ul class="navbar-nav me-auto mt-2 mt-lg-0">
							<li class="nav-item">
								<a class="nav-link text-white fw-bold" href="#" aria-current="page">
									Home
									<span class="visually-hidden">(current)</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link text-white" href="#">Arquivos</a>
							</li>
						</ul>

						<button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#login-modal">
							Login
						</button>
					</div>
				</div>
			</nav>
		</header>

		<main>
			@yield('content')
		</main>

		<footer class="bg-light">
			<strong>
				{{ date('Y') }} | Desenvolvido por Pedro Raposo Felix de Sousa &copy;
			</strong>
		</footer>

		<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="login-modal-label" aria-hidden="true">
			<div class="modal-dialog modal-sm" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="login-modal-label">Login</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="login-form" action="">
							<div class="row">
								<div class="form-group">
									<label for="email" class="form-label">E-mail</label>
									<input type="email" class="form-control" required>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label for="email" class="form-label">Senha</label>
									<input type="password" class="form-control" required>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Fechar</button>
						<button form="login-form" type="submit" class="btn btn-primary">Fazer login</button>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
