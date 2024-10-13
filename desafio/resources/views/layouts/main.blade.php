<!DOCTYPE html>
<html lang="{{ $locale }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', config('app.name'))</title>

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        @stack('css')

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="{{ asset('js/bootstrap/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/script.js') }}" type="module"></script>
        <script>
            localStorage.setItem('locale', @json($locale));
        </script>
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
                                <a @class(['nav-link', 'text-white', 'fw-bold' => request()->is('/')]) href="{{ route('welcome') }}" aria-current="page">
                                    Home

                                    @if(request()->is('/'))
                                        <span class="visually-hidden">(current)</span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item">
                                <a @class(['nav-link', 'text-white', 'fw-bold' => request()->is('arquivos')]) href="{{ route('files') }}">
                                    Arquivos

                                    @if(request()->is('arquivos'))
                                        <span class="visually-hidden">(current)</span>
                                    @endif
                                </a>
                            </li>
                        </ul>

                        <div id="user-profile-container" @class(['dropdown', 'd-none' => !auth()->check()])>
                            <span id="user-profile-name">{{ auth()->user()?->name }}</span>

                            <img src="{{ asset('images/placeholder-avatar.jpg') }}"
                                alt="{{ auth()->user()?->name }}"
                                class="img-fluid rounded-circle dropdown-toggle"
                                data-bs-toggle="dropdown"
                                data-bs-auto-close="outside"
                                aria-expanded="false"
                                width="40"
                                type="button"
                            >

                            <ul class="dropdown-menu">
                                <li>
                                    <button form="logout-form" class="dropdown-item">
                                        <img class="icon me-1" src="{{ asset('images/icons/power-off-solid.svg') }}" />
                                        Sair
                                    </button>
                                    <form id="logout-form" action="{{ route('auth.logout') }}" hidden aria-hidden="true"></form>
                                </li>
                            </ul>
                        </div>

                        <button id="login-btn" @class(['btn btn-light', 'd-none' => auth()->check()])
                            data-bs-toggle="modal"
                            data-bs-target="#login-modal"
                        >
                            Login
                        </button>
                    </div>
                </div>
            </nav>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="d-flex flex-column gap-2 bg-light">
            <strong>
                {{ date('Y') }} | Desenvolvido por Pedro Raposo Felix de Sousa &copy;
            </strong>

            <div class="d-flex gap-2">
                <a href="https://github.com/RaposoHunter" target="_blank" rel="noreferrer">
                    <img class="icon" src="{{ asset('images/icons/github-brands-solid.svg') }}" alt="">
                    GitHub
                </a>
                <a href="https://www.linkedin.com/in/pedro-raposo-8b72301a2/" target="_blank" rel="noreferrer">
                    <img class="icon" src="{{ asset('images/icons/linkedin-brands-solid.svg') }}" alt="">
                    LinkedIn
                </a>
            </div>
        </footer>

        <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="login-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="login-modal-label">Login</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="login-form" action="{{ route('auth.login') }}">
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

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('login-form').addEventListener('submit', async function (event) {
                    event.preventDefault();

                    const form = event.target;

                    const response = await api.post(form.action, {
                        email: form.querySelector('input[type="email"]').value,
                        password: form.querySelector('input[type="password"]').value,
                    });

                    if(!response || !response.ok) {
                        alert(response?.data?.message || 'Algo deu errado ao realizar o login. Tente novamente mais tarde!');
                        return;
                    }

                    localStorage.setItem('token', response.data.token);

                    const container = document.getElementById('user-profile-container');

                    api.get('/me').then(response => {
                        if(!response || !response.ok) return;

                        container.querySelector('img').setAttribute('alt', response.data.name);
                        container.querySelector('#user-profile-name').textContent = response.data.name;
                    });

                    container.classList.remove('d-none');

                    const button = document.getElementById('login-btn');
                    button.classList.add('d-none');

                    document.querySelector('#login-modal button[data-bs-dismiss="modal"]').click();

                    form.reset();
                });

                document.getElementById('logout-form').addEventListener('submit', async function (event) {
                    event.preventDefault();

                    const form = event.target;

                    const response = await api.post(form.action);

                    if(!response || !response.ok) {
                        alert(response?.data?.message || 'Algo deu errado ao realizar o logout. Tente novamente mais tarde!');
                        return;
                    }

                    const container = document.getElementById('user-profile-container');
                    container.classList.add('d-none');
                    container.querySelector('img').setAttribute('alt', '');
                    container.querySelector('#user-profile-name').textContent = '';

                    const button = document.getElementById('login-btn');
                    button.classList.remove('d-none');

                    localStorage.removeItem('token');
                });
            });
        </script>
    </body>
</html>
