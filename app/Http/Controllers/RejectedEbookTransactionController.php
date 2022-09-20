<?php

namespace App\Http\Controllers;

use App\Helpers\MonthHelper;
use App\Models\Author;
use App\Models\Book;
use App\Models\EbookTransaction;
use App\Models\RejectedEbookTransaction;
use Illuminate\Http\Request;

class RejectedEbookTransactionController extends Controller
{
    public function index()
    {
        return view('rejecteds.ebooks.index', [
            'rejected_ebooks' => RejectedEbookTransaction::orderBy('created_at', 'DESC')->paginate(10)
        ]);
    }


    public function delete(RejectedEbookTransaction $rejected_ebook)
    {
        $rejected_ebook->delete();
        return back();
    }

    public function edit(RejectedEbookTransaction $rejected_ebook)
    {
        $authors = Author::all();
        $months = MonthHelper::getMonths();
        return view('rejecteds.ebooks.edit')
            ->with('ebook', $rejected_ebook)
            ->with('authors', $authors)
            ->with('months', $months);
    }

    public function update(Request $request, RejectedEbookTransaction $rejected_ebook)
    {
        $request->validate([
            'author' => 'required',
            'book' => 'required',
            'year' => 'required',
            'month' => 'required',
            'quantity' => 'required',
            'price' => 'required',
            'proceeds' => 'required'
        ]);

        $book = Book::where('title', $request->book)->first();

        if (!$book) {
            $book = Book::create([
                'title' => $request->book
            ]);
        }

        EbookTransaction::create([
            'author_id' => $request->author,
            'book_id' => $book->id,
            'year' => $request->year,
            'month' => $request->month,
            'flag' => $request->flag,
            'format' => $request->format,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'proceeds' => $request->proceeds,
            'royalty' => number_format((float)($request->quantity * $request->price) * 0.15, 2)
        ]);

        $rejected_ebook->delete();

        return redirect(route('rejecteds-ebooks.index'));
    }
}
