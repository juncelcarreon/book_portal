<?php

namespace App\Http\Controllers;

use App\Helpers\MonthHelper;
use App\Models\Author;
use App\Models\Book;
use App\Models\EbookTransaction;
use Illuminate\Http\Request;

class EbookController extends Controller
{
    public function index()
    {
        return view('ebook.index', [
            'ebook_transactions' => EbookTransaction::orderBy('created_at', 'DESC')->paginate(10)
        ]);
    }

    public function create()
    {
        $months = MonthHelper::getAlternativeMonth();
        $authors = Author::all();
        $books = Book::all();
        return view('ebook.create', compact('months', 'authors', 'books'));
    }

    public function importPage()
    {
        return view('ebook.import');
    }

    public function store(Request $request)
    {
        $request->validate([
            'author' => 'required',
            'book' => 'required',
            'year' => 'required',
            'month' => 'required',
            'quantity' => 'required',
            'price' => 'required'
        ]);

        $ebook = EbookTransaction::create([
            'author_id' => $request->author,
            'book_id' => $request->book,
            'year' => $request->year,
            'month' => $request->month,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'royalty' => number_format((float)($request->quantity * $request->price))
        ]);

        return redirect(route('ebook.create'))->with('success', 'Transaction successfully saved');
    }

    public function edit(EbookTransaction $ebook)
    {
        $months = MonthHelper::getAlternativeMonth();
        $authors = Author::all();
        $books = Book::all();
        return view('ebook.edit', compact('ebook', 'months', 'authors', 'books'));
    }
}
