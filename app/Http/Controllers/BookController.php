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

    public function edit(Book $book)
    {
        return view('book.edit', compact('book'));
    }

    public function delete(Book $book)
    {

    }
}
