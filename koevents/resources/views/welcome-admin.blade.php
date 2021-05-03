@extends('layouts.main-admin')

@section('title', 'King Of Events')

@section('content')

<div id="search-container" class="cor-md12">
  
  <h1>Busque um Evento</h1>
  <form action="/admin" method="GET">
  <input type="text" id="search" name="search"class="form-control" placeholder="Procurar...">
  </form>
</div>
<div id="events-container" class="col-m-12">
@if($search)
<h2 align="center">Buscando por: {{$search}}</h2><br><br>
@else
<h2 align="center">Próximos Eventos</h2><br><br>
@endif
<p class="subtitle">Veja os eventos dos próximos dias</p>
<div id="cards-container" class="row">
@foreach($events as $event)
<div class="card col-md-3">

  <img src="/img/events/{{$event->image}}" alt="{{ $event->title }}">
  <div class="card-bordy">
  <p class="card-date">{{ date('d/m/Y', strtotime($event->date))}}</p>
  <h5 class="card-title">{{ $event->title }}</h5>
  <p class="card-participants">{{count($event->users)}} Participantes</p>
  <a href="/events/{{ $event->id }}"class="btn btn-primary">Saber Mais</a>
  </div>
</div>

@endforeach
@if(count($events) == 0 && $search )

 <p>Não foi possível encontrar nenhum evento com {{$search}}! <a href="/admin">Veja Todos Disponíveis</a></p>

@elseif(count($events) == 0 )

<p>Não há eventos disponíveis</p>
@endif 
</div>
</div>

@endsection