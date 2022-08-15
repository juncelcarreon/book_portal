<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\EbookTransaction;
use App\Models\PodTransaction;
use Illuminate\Http\Request;
use PDF;

class GeneratePdfController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'author' => 'required',
            'book' => 'required',
            'fromYear' => 'required',
            'fromMonth' => 'required',
            'toYear' => 'required',
            'toMonth' => 'required'
        ]);

        if($request->fromMonth > $request->toMonth){
            return back()->withErrors(['fromMonth' => 'Date From Month should not be greater than Date To Month']);
        }

        $author = Author::find($request->author);
        $book = Book::find($request->book);

        $podTransactions = PodTransaction::where('author_id', $request->author)->where('book_id', $request->book)
                                ->where('year', '>=', $request->fromYear)->where('year', '<=', $request->toYear)
                                ->where('month', '>=', $request->fromMonth)->where('month', '<=', $request->toMonth)->get();

        $years = [];
        $months = [];
        foreach($podTransactions as $pod)
        {
            if(!in_array($pod->year, $years)){
                array_push($years, $pod->year);
            }
            if(!in_array($pod->month, $months)){
                array_push($months, $pod->month);
            }
        }

        $pods = collect();
        foreach($years as $year)
        {
            foreach($months as $month){
                $pod = $podTransactions->where('year', $year)->where('month', $month)->first();
                if($pod->format == 'Hardback'){
                    $quantity = $podTransactions->where('year', $year)->where('month', $month)->where('format', 'Hardback')->sum('quantity');
                    $royalty = $podTransactions->where('year', $year)->where('month', $month)->where('format', 'Hardback')->sum('royalty');
                    $pods->push(['title' => $pod->book->title, 'year' => $year, 'month' => $month, 'format' => 'Hardback', 'quantity' => $quantity, 'price' => $pod->price, 'royalty' => $royalty]);
                }else{
                    $quantity = $podTransactions->where('year', $year)->where('month', $month)->where('format', 'Paperback')->sum('quantity');
                    $royalty = $podTransactions->where('year', $year)->where('month', $month)->where('format', 'Paperback')->sum('royalty');
                    $pods->push(['title' => $pod->book->title, 'year' => $year, 'month' => $month, 'format' => 'Paperback', 'quantity' => $quantity, 'price' => $pod->price, 'royalty' => $royalty]);
                }
            }
        }

        $ebookTransactions = EbookTransaction::where('author_id', $request->author)->where('book_id', $request->book)
                                ->where('year', '>=', $request->fromYear)->where('month', '>=', $request->fromMonth)
                                ->where('year', '<=', $request->toYear)->where('month', '<=', $request->toMonth)->get();

        // dd($pods);

        return view('report.pdf', [
            'pods' => $pods
        ]);
        // $pdf = PDF::loadView('report.pdf');
        // return $pdf->download('file.pdf');
    }

}
