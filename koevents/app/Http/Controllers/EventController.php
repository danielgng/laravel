<?php

namespace App\Http\Controllers;

use App\Exception\destroyException;
use Illuminate\Http\Request;
use App\Event;
use App\Models\User;

class EventController extends Controller
{
    public function index(){
/**Aqui estarei pegando todos os dados localizados no Model Event */

       $search = request('search');

       if($search){
           $events = Event::where([
             ['title', 'like', '%'.$search.'%']
           ])->get();

       }else{

        $events = Event::all();

       }
       
    
        return view('welcome', ['events'=>$events, 'search'=>$search]);
    }

    public function indexAdm(){
      /**Aqui estarei pegando todos os dados localizados no Model Event */
      
             $search = request('search');
      
             if($search){
                 $events = Event::where([
                   ['title', 'like', '%'.$search.'%']
                 ])->get();
      
             }else{
      
              $events = Event::all();
      
             }
             
          
              return view('welcome-admin', ['events'=>$events, 'search'=>$search]);
          }

  public function create(){
    return view('events.create');
  }
/*Esta função ira trazer todos os dados digitados no formulario create */
 public function store(Request $request){
    try {
      $event = new Event;

    $event->title = $request->title;
    $event->date = $request->date;
    $event->city = $request->city;   
    $event->description = $request->description;
    $event->private = $request->private;
    $event->items = $request->items;
    
   //Upload da imagem

   if($request->hasfile('image') && $request->file('image')->isValid()){

    $requestImage = $request->image;

    $extension= $requestImage->extension();

    $imageName = md5($requestImage->getClientOriginalName()
     . strtotime("now") . "." .$extension);

    $requestImage->move(public_path('img/events'), $imageName);

    $event->image = $imageName; 
   }

   $user= auth()->user();

   $event->user_id = $user->id;
    
   $event->save();

   return redirect('/admin')->with('msg','Evento criado com sucesso!');
    } catch (\Throwable $th) {
      return redirect('/events/create')->with('msg','Error ao criar evento, você não preenheu todos os campos!');
    }

 }

 public function show($id){

  $event= Event::findOrfail($id);

  $user = auth()->user();
  $hasUserJoined= false;

  if($user){
    
    $userEvents = $user->eventsAsParticipant->toArray();
         
    foreach($userEvents as $userEvent){
      if($userEvent['id'] == $id){
      $hasUserJoined = true;
      } 
    }
  }
   
  $eventOwner = User::where('id', $event->user_id)->first()->toArray();

  return view('events.show', ['event'=>$event,
   'eventOwner'=>$eventOwner,
   'hasUserJoined'=>$hasUserJoined]);
  
}
 public function dashboard(){
   $user = auth()->user();

   $events = $user->events;

   $eventsAsParticipant = $user->eventsAsParticipant;

   return view('events.dashboard',
    ['events'=>$events, 'eventsAsParticipant'=>$eventsAsParticipant]);
 }

 public function dashboardAd(){
  $user = auth()->user();

  $events = $user->events;

  $eventsAsParticipant = $user->eventsAsParticipant;

  return view('events.dashboard-admin',
   ['events'=>$events, 'eventsAsParticipant'=>$eventsAsParticipant]);
}

 public function destroy($id){

  try {

   $user= auth()->user();
  
  Event::findOrfail($id)->delete();

  return redirect('/dashboard-admin')->with('msg', 'Evento excluído com sucesso!');
  } catch (\Throwable $th) {

    return redirect('/dashboard-admin')->with('msg','Você não pode excluir um evento com participante!');
  }
 }


 public function edit($id){

  $user= auth()->user();

  $event = Event::findOrfail($id);

  if($user->id != $event->user_id){
    return redirect('/dashboard-admin');
  }

  return view('events.edit', ['event' => $event]);

 }

 

 public function update(Request $request){

  $data = $request->all();

  if($request->hasfile('image') && $request->file('image')->isValid()){

    $requestImage = $request->image;

    $extension= $requestImage->extension();

    $imageName = md5($requestImage->getClientOriginalName()
     . strtotime("now") . "." .$extension);

    $requestImage->move(public_path('img/events'), $imageName);

    $data['image'] = $imageName; 
   }

   Event::findOrfail($request->id)->update($data);
  
   return redirect('/dashboard-admin')->with('msg', 'Evento editado com sucesso!');

 }

 public function joinEvent($id){
   
   $user = auth()->user();

   $user->eventsAsParticipant()->attach($id);

   $event = Event::findOrfail($id);


   return redirect('/dashboard')->with('msg', 'Sua presença foi confirmada no evento' . $event->title);

 }

 public function leaveEvent($id){
   
  $user= auth()->user();

  $user->eventsAsParticipant()->detach($id);

  $event = Event::findOrfail($id);

  return redirect('/dashboard')->with('msg', 'Sua presença foi retirada do evento com sucesso' . $event->title);

 }

 public function showAdm($id){

  $event= Event::findOrfail($id);

  $user = auth()->user();
  $hasUserJoined= false;

  if($user){
    
    $userEvents = $user->eventsAsParticipant->toArray();
         
    foreach($userEvents as $userEvent){
      if($userEvent['id'] == $id){
      $hasUserJoined = true;
      } 
    }
  }
   
  $eventOwner = User::where('id', $event->user_id)->first()->toArray();

  return view('events.show-admin', ['event'=>$event,
   'eventOwner'=>$eventOwner,
   'hasUserJoined'=>$hasUserJoined]);
  
}
 
}




