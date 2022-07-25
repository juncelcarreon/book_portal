<?php

namespace App\Http\Controllers;

use App\Imports\BooksImport;
use App\Models\Book;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BookController extends Controller
{
    public function index()
    {
        return view('book.index',[
            'books' => Book::paginate(10)
        ]);
    }

    public function importPage()
    {
        return view('book.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        Excel::import(new BooksImport, $request->file('file')->store('temp'));
        return back();
    }

    public function create()
    {
        return view('book.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'title' => 'required',
        ]);

        Book::create($request->all());

        return redirect(route('book.create'))->with('success','Book successfully added to database');
    }

    public function edit(Book $book)
    {
        return view('book.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'product_id' => 'required',
            'title' => 'required',
        ]);

        $book->update($request->all());

        return redirect()->route('book.edit', ['book' => $book])->with('success','Book successfully updated to the database');
    }

    public function delete(Book $book)
    {
        $book->delete();

       return redirect()->route('book.index')->with('success','Author has been successfully deleted from the database');
    }
}
