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

        if($request->fromYear > $request->toYear){
            return back()->withErrors(['fromYear' => 'Date From Year should not be greater than Date To Year']);
        }

        if($request->fromMonth > $request->toMonth){
            return back()->withErrors(['fromMonth' => 'Date From Month should not be greater than Date To Month']);
        }

        $author = Author::find($request->author);
        $book = Book::find($request->book);

        $podTransactions = PodTransaction::where('author_id', $request->author)->where('book_id', $request->book)
                                ->where('year', '>=', $request->fromYear)->where('year','<=', $request->toYear)
                                ->where('month', '>=', (int) $request->fromMonth )->where('month', '<=', (int) $request->toMonth)
                                ->get();


        if(count($podTransactions) > 0){

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
                    $podFirst = $podTransactions->where('author_id', $request->author)->where('book_id', $request->book)->where('year', $year)->where('month', $month)->first();
                    if($podFirst){
                        if($podFirst->format == 'Hardback'){
                            $quantity = $podTransactions->where('year', $year)->where('month', $month)->where('format', 'Hardback')->sum('quantity');
                            $royalty = number_format((float)$podTransactions->where('year', $year)->where('month', $month)->where('format', 'Hardback')->sum('royalty'), 2);
                            $pods->push(['title' => $podFirst->book->title, 'year' => $year, 'month' => $month, 'format' => 'Hardback', 'quantity' => $quantity, 'price' => $podFirst->price, 'royalty' => $royalty]);
                        }else{
                            $quantity = $podTransactions->where('year', $year)->where('month', $month)->where('format', 'Paperback')->sum('quantity');
                            $royalty = number_format((float)$podTransactions->where('year', $year)->where('month', $month)->where('format', 'Paperback')->sum('royalty'), 2);
                            $pods->push(['title' => $podFirst->book->title, 'year' => $year, 'month' => $month, 'format' => 'Paperback', 'quantity' => $quantity, 'price' => $podFirst->price, 'royalty' => $royalty]);
                        }
                    }
                }
            }
        }

        $ebookTransactions = EbookTransaction::where('author_id', $request->author)->where('book_id', $request->book)->get();

        $years = [];
        $months = [];
        foreach($ebookTransactions as $ebook)
        {
            if(!in_array($ebook->year, $years)){
                array_push($years, $ebook->year);
            }
            if(!in_array($ebook->month, $months)){
                array_push($months, $ebook->month);
            }
        }

        $ebooks = collect();
        foreach($years as $year)
        {
            foreach($months as $month){
                $ebook = $ebookTransactions->where('year', $year)->where('month', $month)->first();
                if($ebook){
                    $quantity = $ebookTransactions->where('year', $year)->where('month', $month)->sum('quantity');
                    $royalty = number_format((float)$ebookTransactions->where('year', $year)->where('month', $month)->sum('royalty'), 2);
                    $ebooks->push(['title' => $ebook->book->title, 'year' => $year, 'month' => $month,'quantity' => $quantity, 'price' => $ebook->price, 'royalty' => $royalty]);
                }
            }
        }
        $totalPOD = [];
        if(count($podTransactions) > 0){
            $totalPOD['title']= $podTransactions[0]->book->title ." Total";
            $totalPOD['quantity'] = $podTransactions->sum('quantity');
            $totalPOD['royalty'] = number_format((float)$podTransactions->sum('royalty'), 2);
            $totalPOD['price'] = $podTransactions[0]->price;
        }


        $totalEbook = [];
        if(count($ebookTransactions) > 0){
            $totalEbook['title'] = $ebookTransactions[0]->book->title .' Total';
            $totalEbook['quantity'] = $ebookTransactions->sum('quantity');
            $totalEbook['royalty'] = number_format((float)$ebookTransactions->sum('royalty'), 2);
            $totalEbook['price'] = $ebookTransactions[0]->price;
        }

        $author = Author::find($request->author);
        $totalRoyalties = number_format((float) $totalPOD['royalty'] ?? 0 + $totalEbook['royalty'] ?? 0, 2);

        // pods, ebooks, totalPOD, totalEbook, author

        // return view('report.pdf', [
        //     'pods' => $pods,
        //     'ebooks' => $ebooks,
        //     'totalPOD' => $totalPOD,
        //     'totalEbook' => $totalEbook,
        //     'author' => $author,
        //     'totalRoyalties' => $totalRoyalties
        // ]);
        $pdf = PDF::loadView('report.pdf',[
            'pods' => $pods,
            'ebooks' => $ebooks,
            'totalPOD' => $totalPOD,
            'totalEbook' => $totalEbook,
            'author' => $author,
            'totalRoyalties' => $totalRoyalties,
            'fromYear' => $request->fromYear,
            'fromMonth' => $request->fromMonth,
            'toYear' => $request->toYear,
            'toMonth' => $request->toMonth
        ]);
        return $pdf->download('file.pdf');

    }
}

