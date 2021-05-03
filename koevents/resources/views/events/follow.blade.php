@extends('layouts.main')

@section('title', 'Fale Conosco')

@section('content')

<div id="follow-container" class="col-md-6 offset-md-3">
  <h1>Fale Conosco</h1>
  <form action="/follow" method="POST" enctype="multipart/form-data">
  @csrf
  
  <div class="form-group">
  <label for="title">Nome:</label>
  <input type="text" class="form-control" id="name" name="name" placeholder="Digite seu nome">
  </div>

  <div class="form-group">
  <label for="title">Data de nascimento:</label>
  <input type="date" class="form-control" id="date" name="date">
  </div>
  
  <div class="form-group">
  <label for="title">Telefone:</label>
  <input type="text" class="form-control" id="number" name="number" placeholder="Digite seu numero">
  </div>

  <div class="form-group">
  <label for="title">E-mail:</label>
  <input type="email" class="form-control" id="email" name="email" placeholder="Digite seu email">
  </div>

  <div class="form-group">
  <label for="title">Descrição:</label>
  <textarea name="description" id="description" class="form-control" placeholder="Qual o motivo de nos contactar"></textarea>
  </div>

  <br><input type="submit" class="btn btn-primary" value="Enviar">
  </form>
</div>

@endsection
