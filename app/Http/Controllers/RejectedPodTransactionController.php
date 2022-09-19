<?php

namespace App\Http\Controllers;

use App\Helpers\MonthHelper;
use App\Models\Author;
use App\Models\Book;
use App\Models\PodTransaction;
use App\Models\RejectedPodTransaction;
use App\View\Components\RejectedPod;
use Illuminate\Http\Request;

class RejectedPodTransactionController extends Controller
{
    public function index()
    {
        $books = Book::all();
        return view('rejecteds.pods.index', [
            'pods' => RejectedPodTransaction::orderBy('created_at', 'DESC')->paginate(10)
        ], compact('books'));
    }


    public function delete(RejectedPodTransaction $rejected_pod)
    {
        $rejected_pod->delete();
        return back();
    }

    public function edit(RejectedPodTransaction $rejected_pod)
    {
        $authors = Author::all();
        $months = MonthHelper::getMonths();
        return view('rejecteds.pods.edit')
            ->with('pod', $rejected_pod)
            ->with('authors', $authors)
            ->with('months', $months);
    }

    public function update(Request $request, RejectedPodTransaction $rejected_pod)
    {
        $request->validate([
            'author' => 'required',
            'book' => 'required',
            'year' => 'required',
            'month' => 'required',
            'flag' => 'required',
            'format' => 'required',
            'quantity' => 'required',
            'price' => 'required',
        ]);

        $book = Book::where('title', $request->book)->first();

        if (!$book) {
            $book = Book::create([
                'title' => $request->book
            ]);
        }

        PodTransaction::create([
            'author_id' => $request->author,
            'book_id' => $book->id,
            'year' => $request->year,
            'month' => $request->month,
            'flag' => $request->flag,
            'format' => $request->format,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'royalty' => number_format((float)($request->quantity * $request->price) * 0.15, 2)
        ]);

        $rejected_pod->delete();

        return redirect(route('rejecteds-pods.index'));
    }
}
