<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use App\Http\Requests\BookRequest;
use Illuminate\Support\Facades\Auth;


class BookController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except('index', 'show');
    }

    public function index()
    {
        $books = Book::all();
        $authors = Author::all();
        return view('books.index', compact('books', 'authors'));
    }

    public function create()
    {
        $authors = Author::all();
        $categories = Category::all();

        return view('books.create', compact('authors', 'categories'));
    }

    public function store(BookRequest $request)
    {
        
        $path_image='';
        if($request->hasFile('image') && $request->file('image')->isValid()){
            $path_name=$request->file('image')->getClientOriginalName();
            $path_image=$request->file('image')->storeAs('public/images',$path_name);
        }
        
        $data = Book::create([
            'title'=>$request->title,
            'author_id'=>$request->author_id,
            'year'=>$request->year,
            'pages'=>$request->pages,
            'image'=>$path_image,
            'user_id'=>Auth::user()->id, 
        ]);

        $data->categories()->attach($request->categories);
        return redirect()->route('books.index')->with('success', 'Libro inserito con successo');
    }

    public function show($book)
    {
        $book = Book::findOrFail($book);
        $category = Category::all();

        return view('books.show', ['book'=>$book, 'category'=>$category]);
    }

    public function edit(Book $book) 
    {
        if((Auth::user()->id != $book->user_id))
        {
            abort(404);
        }

        $authors = Author::all();
        $categories = Category::all();
        return view('books.edit', compact('book', 'authors', 'categories'));
    }

    public function update(BookRequest $request, Book $book) 
    {
        $path_image = $book->img;

        if ($request->hasFile('img') && $request->file('img')->isValid()) {
            $path_name = $request->file('img')->getClientOriginalName();
            $path_image = $request->file('img')->storeAs('public/images/cover', $path_name);
        }

        $book->update([
            'title'=>$request->title,
            'author_id'=>$request->author_id,
            'year'=>$request->year,
            'pages'=>$request->pages,
            'image'=>$path_image,
            'user_id'=>Auth::user()->id,
        ]);
        $book->categories()->sync($request->categories); //Metodo 2 in 1

        return redirect()->route('books.index')
            ->with('success', 'Libro eliminato correttamente');
    }

    public function destroy(Book $book)
    {
        $book->categories()->detach();
        $book->delete();
        
        return redirect()->route('books.index')->with('success', 'Libro eliminato con successo');
    }
}
