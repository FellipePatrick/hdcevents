<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;

class EventController extends Controller
{
    public function index(){
        $search = request('search');
        
        if($search) {

            $events = Event::where([
                ['title', 'like', '%'.$search.'%']
            ])->get();

        } else {
            $events = Event::all();
        }        
    
        return view('welcome',['events' => $events, 'search' => $search]);
    }

    public function create(){
        return view('events.create');
    }

    public function store(Request $request){
        $event = new Event;
        $event->title  = $request->title;
        $event->description  = $request->description;
        $event->private  = $request->private;
        $event->city  = $request->city;
        $event->date = $request->date;
        $event->items = $request->items;
             // Image Upload
        if($request->image == Null){
             $event->image = '';
        }else{
                if($request->hasFile('image') && $request->file('image')->isValid()) {

                    $requestImage = $request->image;

                    $extension = $requestImage->extension();
            
                    $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;
            
                    $requestImage->move(public_path('img/events'), $imageName);
            
                    $event->image = $imageName;
            
                }
        }

        $user = auth()->user();
        $event->user_id = $user->id;
        
        $event->dono = $user->name;

        $event->save();
        
        return redirect('/')->with('msg', 'Evento Criado');
    }
    public function show($id){
        $event = Event::findOrFail($id);
        return view('events.show', ['event'=>$event]);
    }

    public function edit($id){
        $event = Event::findOrFail($id);
        return view('events.edit', ['event'=>$event]);
    }


    public function destroy($id){
        Event::findOrFail($id)->delete();
        return redirect('/dashboard')->with('msg', 'Evento deletado com sucesso!');
    }

    public function upgrade(Request $request, $id){
   
        $data = $request->all();

        $event = Event::findOrFail($id);

        if($request->hasFile('image') && $request->file('image')->isValid()) {

            $requestImage = $request->image;

            $extension = $requestImage->extension();
    
            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;
    
            $requestImage->move(public_path('img/events'), $imageName);
    
            $data['image'] = $imageName;
    
        }

        if($request->date == ''){
            $data['date'] = $event->date;
        }
        Event::findOrFail($id)->update($data);
        return redirect('/dashboard')->with('msg', 'Evento Editado com sucesso!');
    }
    public function dashboard(){
        $user = auth()->user();

        $events = $user->events;
        
        return view('events.dashboard', ['events'=>$events]);

    }

    public function joinEvent($id) {

        $user = auth()->user();

        $user->eventsAsParticipant()->attach($id);

        $event = Event::findOrFail($id);

        return redirect('/dashboard')->with('msg', 'Sua presença está confirmada no evento ' . $event->title);

    }


}
