<?php

namespace App\Http\Controllers;

use App\Helpers\MonthHelper;
use App\Helpers\SampleHelper;
use App\Imports\PodTransactionsImport;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Models\PodTransaction;
use Maatwebsite\Excel\Facades\Excel;

class PodTransactionController extends Controller
{
    public function index()
    {
        return view('pod.index', [
            'pod_transactions' => PodTransaction::orderBy('created_at', 'DESC')->paginate(10)
        ]);
    }

    public function create()
    {
        $months = MonthHelper::getAlternativeMonth();
        $authors = Author::all();
        $books = Book::all();
        return view('pod.create', compact('months', 'authors', 'books'));
    }

    public function importPage()
    {
        return view('pod.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        Excel::import(new PodTransactionsImport, $request->file('file')->store('temp'));
        dd(SampleHelper::getCount());
        return back()->with('success', 'Data successfully imported');
    }

    public function store(Request $request)
    {
        $request->validate([
            'author' => 'required',
            'book_title' => 'required',
            'year' => 'required',
            'month' => 'required',
            'flag' => 'required',
            'format' => 'required',
            'quantity' => 'required',
            'price' => 'required',
        ]);

        $pod = PodTransaction::create([
            'author_id' =>$request->author,
            'book_id' => $request->book_title,
            'year' => $request->year,
            'month' => $request->month,
            'flag' => $request->flag,
            'status' => $request->status,
            'format' => $request->format,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'royalty' => number_format((float)($request->quantity * $request->price) * 0.15, 2)
        ]);

        return redirect(route('pod.create'))->with('success', 'Transaction successfully saved');
    }

    public function delete(PodTransaction $pod)
    {
        $pod->delete();
       return redirect()->route('pod.index')->with('success','Transaction successfully deleted');
    }

    public function edit(PodTransaction $pod)
    {
        $months = MonthHelper::getAlternativeMonth();
        $authors = Author::all();
        $books = Book::all();
        return view('pod.edit', compact('pod', 'months', 'authors', 'books'));
    }

    public function update(Request $request, PodTransaction $pod)
    {
        $request->validate([
            'author' => 'required',
            'book_title' => 'required',
            'year' => 'required',
            'month' => 'required',
            'flag' => 'required',
            'format' => 'required',
            'quantity' => 'required',
            'price' => 'required',
        ]);

        $pod -> update([
            'author_id' =>$request->author,
            'book_id' => $request->book_title,
            'year' => $request->year,
            'month' => $request->month,
            'flag' => $request->flag,
            'status' => $request->status,
            'format' => $request->format,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'royalty' => number_format((float)($request->quantity * $request->price) * 0.15, 2)
        ]);

        return redirect(route('pod.edit'))->with('success', 'Transaction successfully updated');

    }
}
