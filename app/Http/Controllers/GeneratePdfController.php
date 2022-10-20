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
    /*
        CHANGE LOG

        2022-10-23:
            - Show highest Retails Price from each Format and Total Retail
                * Juncel
    */
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
                $totalPods = collect(['title' => 'Grand Total', 'quantity' =>  0, 'price' => 0, 'revenue'=> 0, 'royalty' => 0]);
                foreach($request->book as $book){
                    $podTransactions = PodTransaction::where('author_id', $request->author)->where('book_id', $book)
                                            ->where('year', '>=', $request->fromYear)->where('year','<=', $request->toYear)
                                            ->where('month', '>=', (int) $request->fromMonth )->where('month', '<=', (int) $request->toMonth)
                                            ->where('royalty', '<>', 0)
                                            ->get();


                    if(count($podTransactions) > 0){
                      
                        $years = [];
                        $months = [];
                        foreach($podTransactions as $key=>$pod)
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

                                if($podFirst  ){
                                    $perfectbound = $podTransactions->where('year', $year)->where('month', $month)->where('format', 'Perfectbound');
                                    $paperBackquan = 0;
                                    $paperRev = 0;
                                    $paperHigh = 0;
                                    foreach ($perfectbound as $pod) {
                                        $paperBackquan += $pod->quantity;
                                        $paperRev += $pod->price * $pod->quantity;
                                        if($pod->price > $paperHigh) {
                                            $paperHigh = $pod->price;
                                        }
                                    }

                                    //  = $podTransactions->where('year', $year)->where('month', $month)->where('format', 'Perfectbound')->sum('quantity');
                                    $paperRev  = number_format($paperRev ,2);
                                    $royalty = number_format((float)$paperRev * 0.15 ,2);
                                    $pods->push(['title' => $podFirst->book->title, 'year' => $year, 'month' => $month, 'format' => 'Paperback', 'quantity' => $paperBackquan, 'price' => $paperHigh, 'revenue'=> $paperRev, 'royalty' => $royalty]);

                                    $hardBound = $podTransactions->where('year', $year)->where('month', $month)->where('format', '!=', 'Perfectbound');
                                    $hardBackQuan = 0;
                                    $hardbackRev = 0;
                                    $hardHigh = 0;
                                    foreach ($hardBound as $pod) {
                                        $hardBackQuan += $pod->quantity;
                                        $hardbackRev += $pod->price * $pod->quantity;
                                        if($pod->price > $hardHigh) {
                                            $hardHigh = $pod->price;
                                        }
                                    }

                                        // $hardBackQuan = $podTransactions->where('year', $year)->where('month', $month)->where('format', '!=', 'Perfectbound');
                                        // $price = $podTransactions->where('year', $year)->where('month', $month)->where('format', 'Trade Cloth/Laminate')->first();
                                        // $hardbackRev  = number_format((float)$price->price * $hardBackQuan ,2);
                                        $hardbackRev  = number_format($hardbackRev ,2);
                                        $royalty = number_format((float)$hardbackRev * 0.15 ,2);

                                            $pods->push(['title' => $podFirst->book->title, 'year' => $year, 'month' => $month, 'format' => 'Hardback', 'quantity' =>  $hardBackQuan, 'price' =>$hardHigh ,'revenue'=> $hardbackRev, 'royalty' => $royalty]);
                                     }
                                    
                                
                            }
                        }

                        $pods->push([
                            'title' => $podTransactions[0]->book->title . " Total",
                            'quantity' => $paperBackquan + $hardBackQuan,
                            'revenue' => (float) $paperRev + (float) $hardbackRev,
                            'royalty' => number_format((float)$podTransactions->sum('royalty'), 2),
                            'price' => (($paperHigh > $hardHigh) ? $paperHigh : $hardHigh)
                        ]);

                    }
                }
        
        
                foreach($pods as $pod){
                    if(UtilityHelper::hasTotalString($pod)){
                        $totalPods->put('quantity',$totalPods['quantity'] + $pod['quantity']);
                        $totalPods->put('revenue',$totalPods['revenue'] + $pod['revenue']);
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
                $totalPods = collect(['title' => 'Grand Total', 'quantity' =>  0, 'price' => 0, 'revenue'=> 0, 'royalty' => 0]);
                foreach($request->book as $book){
                    $podTransactions = PodTransaction::where('author_id', $request->author)->where('book_id', $book)
                                            ->where('year', '>=', $request->fromYear)->where('year','<=', $request->toYear)
                                            ->where('month', '>=', (int) $request->fromMonth )->where('month', '<=', (int) $request->toMonth)
                                            // ->where('royalty', '<>', 0)
                                            ->get();


                    if(count($podTransactions) > 0){
                      
                        $years = [];
                        $months = [];
                        foreach($podTransactions as $key=>$pod)
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

                                if($podFirst  ){
                                    $perfectbound = $podTransactions->where('year', $year)->where('month', $month)->where('format', 'Perfectbound');
                                    $paperBackquan = 0;
                                    $paperRev = 0;
                                    $paperHigh = 0;
                                    foreach ($perfectbound as $pod) {
                                        $paperBackquan += $pod->quantity;
                                        $paperRev += $pod->price * $pod->quantity;
                                        if($pod->price > $paperHigh) {
                                            $paperHigh = $pod->price;
                                        }
                                    }

                                    //  = $podTransactions->where('year', $year)->where('month', $month)->where('format', 'Perfectbound')->sum('quantity');
                                    $paperRev  = number_format($paperRev ,2);
                                    $royalty = number_format((float)$paperRev * 0.15 ,2);
                                    $pods->push(['title' => $podFirst->book->title, 'year' => $year, 'month' => $month, 'format' => 'Paperback', 'quantity' => $paperBackquan, 'price' => $paperHigh, 'revenue'=> $paperRev, 'royalty' => $royalty]);

                                    $hardBound = $podTransactions->where('year', $year)->where('month', $month)->where('format', '!=', 'Perfectbound');
                                    $hardBackQuan = 0;
                                    $hardbackRev = 0;
                                    $hardHigh = 0;
                                    foreach ($hardBound as $pod) {
                                        $hardBackQuan += $pod->quantity;
                                        $hardbackRev += $pod->price * $pod->quantity;
                                        if($pod->price > $hardHigh) {
                                            $hardHigh = $pod->price;
                                        }
                                    }

                                        // $hardBackQuan = $podTransactions->where('year', $year)->where('month', $month)->where('format', '!=', 'Perfectbound');
                                        // $price = $podTransactions->where('year', $year)->where('month', $month)->where('format', 'Trade Cloth/Laminate')->first();
                                        // $hardbackRev  = number_format((float)$price->price * $hardBackQuan ,2);
                                        $hardbackRev  = number_format($hardbackRev ,2);
                                        $royalty = number_format((float)$hardbackRev * 0.15 ,2);

                                            $pods->push(['title' => $podFirst->book->title, 'year' => $year, 'month' => $month, 'format' => 'Hardback', 'quantity' =>  $hardBackQuan, 'price' =>$hardHigh ,'revenue'=> $hardbackRev, 'royalty' => $royalty]);
                                     }
                            }
                        }

                        $pods->push([
                            'title' => $podTransactions[0]->book->title . " Total",
                            'quantity' => $paperBackquan + $hardBackQuan,
                            'revenue' => number_format((float) $paperRev + (float) $hardbackRev, 2),
                            'royalty' => number_format((float)$podTransactions->sum('royalty'), 2),
                            'price' => (($paperHigh > $hardHigh) ? number_format($paperHigh, 2) : number_format($hardHigh, 2))
                        ]);

                    }
                }

                $grand_price = 0;
                $grand_revenue = 0;
                foreach($pods as $pod){
                    if(UtilityHelper::hasTotalString($pod)){
                        $totalPods->put('quantity',$totalPods['quantity'] + $pod['quantity']);
                        $totalPods->put('royalty', $totalPods['royalty'] + $pod['royalty']);
                        $grand_revenue += $pod['revenue'];
                    }

                    if($pod['price'] > $grand_price) {
                        $grand_price = $pod['price'];
                    }
                }
                $totalPods['price'] = number_format($grand_price, 2);
                $totalPods['revenue'] = number_format($grand_revenue, 2);

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