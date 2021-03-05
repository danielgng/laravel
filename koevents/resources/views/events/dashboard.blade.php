@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')

<div class="col-md-10 offset-md-1 dashboard-title-container">
    <h1>Eventos que estou participando</h1>
</div>
<div class="col-md-10 offset-md-1 dashboard-title-container">
    
    @if(count($eventsAsParticipant) > 0)

      <table class="table">
    <thead>
      <tr>
       <th scope="col">#</th>
       <th scope="col">Nome</th>
       <th scope="col">Participantes</th>
       <th scope="col">Ações</th>
      </tr>
    </thead>  
    <tbody>
     @foreach($eventsAsParticipant as $event)
       <tr>
        <td scropt ="row">{{ $loop->index + 1 }}</td>
        <td><a href="/events/{{ $event->id }}">{{ $event->title }}</a></td>
        <td>{{ count( $event->users )}}</td>
        <td>
          <form action="/events/leave/{{$event->id}}" method="POST">
          @csrf
          @method("DELETE")
          <button type="submit" class="btn btn-danger delete-btn">
          <ion-icon name="trash-outline">         
          </ion-icon>
          Sair do Evento
          </button>
          </form>
        </td>
       </tr>
     @endforeach
    </tbody>
    </table>

    @else
<p>Você ainda não está participando de nenhum evento, <a href="/">Veja os eventos disponiveis</a></p>
    @endif
</div>
@endsection
