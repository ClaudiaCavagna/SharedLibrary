<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Requests\BookRequest;


class BookController extends Controller
{

    public function homepage(){
        return view('homepage');
    }
    public function index(){
        $books = Book::all();
        return view('index', ['books'=>$books]);
    }
    public function create(){
        return view('create');
    }
    public function store(BookRequest $request){
        $path_image='';
        if($request->hasFile('image') && $request->file('image')->isValid()){
            $path_name=$request->file('image')->getClientOriginalName();
            $path_image=$request->file('image')->storeAs('public/images',$path_name);
        }
        Book::create([
            'title'=>$request->title,
            'author'=>$request->author,
            'year'=>$request->year,
            'pages'=>$request->pages,
            'image'=>$path_image
        ]);
        return redirect()->route('index')->with('success', 'Libro inserito con successo');
    }
    public function show($book){
        $mybook = Book::find($book);
        if(!$mybook){
            abort(404);
        };
        return view('show', ['book'=>$mybook]);
    }
}