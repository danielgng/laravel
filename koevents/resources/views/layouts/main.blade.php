<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>
        <!--Fonte do Google-->
        <link href="https://fonts.googleapis.com/css2?family=Oswald" rel="stylesheet">
        <!--CSS Bootstrap-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
        <!--CSS da aplicação-->
      <link rel="stylesheet" href="/css/style.css">           
    </head>

    <body>
      <header>
      <nav class="navbar navbar-expand-lg navbar-light">
      <div class="callapse navbar-collapse" id="navbar">
        <a href="/" class="navbar-brand">
        <img src="/img/logo3.png" alt="Logo">
        </a>
        <ul class="navbar-nav">
        <li class="nav-item">
        <a href="/" class="nav-link">Inicio</a>
        </li>
        @auth 
        <li class="nav-item">
        <a href="/dashboard" class="nav-link">Meus Eventos</a>
        </li>
        <li class="nav-item">
        <a href="/follow/create" class="nav-link">Fale Conosco</a>
        </li>
        <li class="nav-item">
        <form action="/logout"method="POST">
        @csrf
        <a href="/logout" class="nav-link"
        onclick="event.preventDefault();
        this.closest('form').submit();">
        Sair</a>
        </form>
        </li>
        @endauth
        @guest
        <li class="nav-item">
        <a href="/login" class="nav-link">Entrar</a>
        </li>
        <li class="nav-item">
        <a href="/register" class="nav-link">Cadastrar</a>
        </li>
        @endguest       
        </ul>
      </div>
      </nav>
      </header>

  

<main>
  <div class="container-fluid">
    <div class="row">
      @if(session('msg'))
      <p class="msg">{{ session('msg') }}</p>
      @endif
      @yield('content')
    </div>
  </div>
</main> 


    <footer>
    <p>King Of Events &copy; 2021</P>
     </footer>
     <script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js"></script>
     
    </body>
</html>
