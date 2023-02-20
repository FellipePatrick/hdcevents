<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function index(){
        $event = Event::all();
        return view('welcome',['events'=> $event]);
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

        $event->save();
        
        return redirect('/')->with('msg', 'Evento Criado');


    }
    public function show($id){
        $event = Event::findOrFail($id);
        return view('events.show', ['event'=>$event]);
    }
}
