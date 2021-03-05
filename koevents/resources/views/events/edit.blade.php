@extends ('layouts.main-admin')

@section('title', 'Editando: ' .$event->title)

@section('content')

<div id="event-container" class="col-md-6 offset-md-3">
  <h1>Editando: {{ $event->title }}</h1>
  <form action="/events/update/{{ $event->id }}" method="POST" enctype="multipart/form-data">
  @csrf
  @method('PUT')
  <div class="form-group">
  <label for="image">Imagem do Evento:</label>
  <input type="file" id="image" name="image" class="form-control-file">
  <img src="/img/events/{{ $event->image }}" alt="{{ $event->title }}" class="img-preview">
  </div>

  <div class="form-group">
  <label for="title">Evento:</label>
  <input type="text" class="form-control" id="title" name="title" placeholder="Nome do evento" value="{{ $event->title }}">
  </div>

  <div class="form-group">
  <label for="title">Data do evento:</label>
  <input type="date" class="form-control" id="date" name="date" value="{{ $event->date->format('Y-m-d')}}">
  </div>
  
  <div class="form-group">
  <label for="title">Cidade:</label>
  <input type="text" class="form-control" id="city" name="city" placeholder="Local do evento" value="{{ $event->city}}">
  </div>

  <div class="form-group">
  <label for="title">Descrição:</label>
  <textarea name="description" id="description" class="form-control"
  placeholder="Descrição sobre o evento">{{ $event->description }}</textarea>
  </div>

  <div class="form-group">
  <label for="title">Adicione items de infraestrutura:</label>

  <div class="form-group">
  <input type="checkbox" name="items[]" value="Cadeiras">Cadeiras
  </div>
  <div class="form-group">
  <input type="checkbox" name="items[]" value="Palco">Palco
  </div>
  <div class="form-group">
  <input type="checkbox" name="items[]" value="Open Bar">Open Bar
  </div>
  <div class="form-group">
  <input type="checkbox" name="items[]" value="Brindes">Brindes
  </div>
  </div>
  <div class="form-group">
  <label for="title">Tipo do evento:</label>
  <select name="private" id="private" class="form-control">
  <option value="1"{{$event->private == 1 ? "selected='selected'" : ""}}>Privado</option>
  <option value="0">Publico</option>
</select>
  </div>
  <br><input type="submit" class="btn btn-primary" value="Editar Evento">
  </form>
</div>
@endsection