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

        $event->save();
        
        return redirect('/');


    }
}
