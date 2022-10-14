<?php

namespace App\Http\Controllers;

use App\Helpers\NumberFormatterHelper;
use App\Helpers\UtilityHelper;
use App\Models\Author;
use App\Models\Book;
use App\Models\EbookTransaction;
use App\Models\PodTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use NumberFormatter;
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
            'toMonth' => 'required',
            'actiontype'=>'required'
        ]);
        
        switch ($request->actiontype) {
            case 'print':
                if($request->fromYear > $request->toYear){
                    return back()->withErrors(['fromYear' => 'Date From Year should not be greater than Date To Year']);
                }
        
                if($request->fromMonth > $request->toMonth){
                    return back()->withErrors(['fromMonth' => 'Date From Month should not be greater than Date To Month']);
                }
        
                $author = Author::find($request->author);
        
                $pods = collect();
                $totalPods = collect(['title' => 'Grand Total', 'quantity' =>  0, 'price' => 0, 'royalty' => 0]);
                foreach($request->book as $book){
                    $podTransactions = PodTransaction::where('author_id', $request->author)->where('book_id', $book)
                                            ->where('year', '>=', $request->fromYear)->where('year','<=', $request->toYear)
                                            ->where('month', '>=', (int) $request->fromMonth )->where('month', '<=', (int) $request->toMonth)
                                            ->where('royalty', '<>', 0)
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
        
        
                        foreach($years as $year)
                        {
                            foreach($months as $month){
                                $podFirst = $podTransactions->where('year', $year)->where('month', $month)->first();
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
                        $pods->push([
                            'title' => $podTransactions[0]->book->title . " Total",
                            'quantity' => $podTransactions->sum('quantity'),
                            'royalty' => number_format((float)$podTransactions->sum('royalty'), 2),
                            'price' => $podTransactions[0]->price
                        ]);
                    }
                }
        
        
                foreach($pods as $pod){
                    if(UtilityHelper::hasTotalString($pod)){
                        $totalPods->put('quantity',$totalPods['quantity'] + $pod['quantity']);
                        $totalPods->put('royalty', $totalPods['royalty'] + $pod['royalty']);
                    }
                }
        
        
                $ebooks = collect();
                $totalEbooks = collect(['title' => 'Grand Total' , 'quantity' => 0, 'royalty' => 0]);
        
                foreach($request->book as $book){
                    $ebookTransactions = EbookTransaction::where('author_id', $request->author)->where('book_id', $book)
                                                ->where('year', '>=', $request->fromYear)->where('year','<=', $request->toYear)
                                                ->where('month', '>=', (int) $request->fromMonth )->where('month', '<=', (int) $request->toMonth)
                                                ->where('royalty', '<>', 0)
                                                ->get();
        
                    if(count($ebookTransactions) > 0){
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
        
                        $ebooks->push([
                            'title' => $ebookTransactions[0]->book->title . " Total",
                            'quantity' => $ebookTransactions->sum('quantity'),
                            'royalty' => number_format((float)$ebookTransactions->sum('royalty'), 2),
                            'price' => $ebookTransactions[0]->price
                        ]);
                    }
        
                }
        
                foreach($ebooks as $ebook){
                    if(UtilityHelper::hasTotalString($ebook)){
                        $totalEbooks->put('quantity',$totalEbooks['quantity'] + $ebook['quantity']);
                        $totalEbooks->put('royalty', $totalEbooks['royalty'] + $ebook['royalty']);
                    }
                }
        
        
                $totalRoyalties = number_format((float) $totalPods['royalty'] + $totalEbooks['royalty'], 2);
                $numberFormatter = NumberFormatterHelper::numtowords($totalRoyalties);
                $currentDate = Carbon::now();
        
                $imageUrl = asset('images/header.png');
    
                $pdf = PDF::loadView('report.pdf',[
                    'pods' => $pods,
                    'ebooks' => $ebooks,
                    'author' => $author,
                    'totalPods' => $totalPods,
                    'totalEbooks' => $totalEbooks,
                    'totalRoyalties' => $totalRoyalties,
                    'fromYear' => $request->fromYear,
                    'fromMonth' => $request->fromMonth,
                    'toYear' => $request->toYear,
                    'toMonth' => $request->toMonth,
                    'numberFormatter' => $numberFormatter,
                    'currentDate' => $currentDate,
                    'imageUrl' => $imageUrl,
                ]);
                return $pdf->download('royalty.pdf');
        
                break;
            
            case'show':
                if($request->fromYear > $request->toYear){
                    return back()->withErrors(['fromYear' => 'Date From Year should not be greater than Date To Year']);
                }
        
                if($request->fromMonth > $request->toMonth){
                    return back()->withErrors(['fromMonth' => 'Date From Month should not be greater than Date To Month']);
                }
        
                $author = Author::find($request->author);
        
                $pods = collect();
                $totalPods = collect(['title' => 'Grand Total', 'quantity' =>  0, 'price' => 0, 'royalty' => 0]);
                foreach($request->book as $book){
                    $podTransactions = PodTransaction::where('author_id', $request->author)->where('book_id', $book)
                                            ->where('year', '>=', $request->fromYear)->where('year','<=', $request->toYear)
                                            ->where('month', '>=', (int) $request->fromMonth )->where('month', '<=', (int) $request->toMonth)
                                            ->where('royalty', '<>', 0)
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
        
        
                        foreach($years as $year)
                        {
                            foreach($months as $month){
                                $podFirst = $podTransactions->where('year', $year)->where('month', $month)->first();
                                if($podFirst){
                                    
                                        $quantity = $podTransactions->where('year', $year)->where('month', $month)->where('format', 'Hardback')->sum('quantity');
                                        $revenue = number_format((float)$podTransactions->where('year', $year)->where('month', $month)->where('format', 'Hardback')->sum('price'), 2);
                                        $royalty = number_format((float)$podTransactions->where('year', $year)->where('month', $month)->where('format', 'Hardback')->sum('royalty'), 2);
                                        $pods->push(['title' => $podFirst->book->title, 'year' => $year, 'month' => $month, 'format' => 'Hardback', 'quantity' => $quantity, 'price' => $podFirst->price, 'royalty' => $royalty]);
                                   
                                        $quantity = $podTransactions->where('year', $year)->where('month', $month)->where('format', 'Paperback')->sum('quantity');
                                        $royalty = number_format((float)$podTransactions->where('year', $year)->where('month', $month)->where('format', 'Paperback')->sum('royalty'), 2);
                                        $pods->push(['title' => $podFirst->book->title, 'year' => $year, 'month' => $month, 'format' => 'Paperback', 'quantity' => $quantity, 'price' => $podFirst->price, 'royalty' => $royalty]);
                                    
                                }
                            }
                        }
                        $pods->push([
                            'title' => $podTransactions[0]->book->title . " Total",
                            'quantity' => $podTransactions->sum('quantity'),
                           
                            'royalty' => number_format((float)$podTransactions->sum('royalty'), 2),
                            'price' => $podTransactions[0]->price
                        ]);
                    }
                }
        
        
                foreach($pods as $pod){
                    if(UtilityHelper::hasTotalString($pod)){
                        $totalPods->put('quantity',$totalPods['quantity'] + $pod['quantity']);
                        $totalPods->put('royalty', $totalPods['royalty'] + $pod['royalty']);
                    }
                }
        
        
                $ebooks = collect();
                $totalEbooks = collect(['title' => 'Grand Total' , 'quantity' => 0, 'royalty' => 0]);
        
                foreach($request->book as $book){
                    $ebookTransactions = EbookTransaction::where('author_id', $request->author)->where('book_id', $book)
                                                ->where('year', '>=', $request->fromYear)->where('year','<=', $request->toYear)
                                                ->where('month', '>=', (int) $request->fromMonth )->where('month', '<=', (int) $request->toMonth)
                                                ->where('royalty', '<>', 0)
                                                ->get();
        
                    if(count($ebookTransactions) > 0){
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
        
                        $ebooks->push([
                            'title' => $ebookTransactions[0]->book->title . " Total",
                            'quantity' => $ebookTransactions->sum('quantity'),
                            'royalty' => number_format((float)$ebookTransactions->sum('royalty'), 2),
                            'price' => $ebookTransactions[0]->price
                        ]);
                    }
        
                }
        
                foreach($ebooks as $ebook){
                    if(UtilityHelper::hasTotalString($ebook)){
                        $totalEbooks->put('quantity',$totalEbooks['quantity'] + $ebook['quantity']);
                        $totalEbooks->put('royalty', $totalEbooks['royalty'] + $ebook['royalty']);
                    }
                }
        
        
                $totalRoyalties = number_format((float) $totalPods['royalty'] + $totalEbooks['royalty'], 2);
                $numberFormatter = NumberFormatterHelper::numtowords($totalRoyalties);
                $currentDate = Carbon::now();
        
                
                return view('prev',[
                    'pods' => $pods,
                    'ebooks' => $ebooks,
                    'author' => $author,
                    'totalPods' => $totalPods,
                    'totalEbooks' => $totalEbooks,
                    'totalRoyalties' => $totalRoyalties,
                    'fromYear' => $request->fromYear,
                    'fromMonth' => $request->fromMonth,
                    'toYear' => $request->toYear,
                    'toMonth' => $request->toMonth,
                    'numberFormatter' => $numberFormatter,
                    'currentDate' => $currentDate,
                   
                ]);
        
                break;
        }
       

    }
}