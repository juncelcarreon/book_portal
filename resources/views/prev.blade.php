@extends('layouts.authenticated')

@section('content')
    <div class="container ">
        <div class="p-3 my-3 w-100 ">
            <div class="d-flex">
           <div class="details" style="margin-top: 30px;">
        <span>{{$currentDate}}</span>
        <h6 class="mt-4" style="font-size: 15px;"></h6>
        <span style="font-size: 15px;">{{$author->address}}</span>
    </div>
    <div class="details" style="margin-top: 30px;">
        <span style="font-size: 15px; mb-5;">Authors Name: <b>{{$author->getFullName()}}</b>,</span>
        <br><br>
        <span style="font-size: 15px;">Enclosed is the royalty payment amounting to <strong>${{$totalRoyalties}}</strong> ({{$numberFormatter}}).</span>
        <br>
        <span  style="font-size: 15px;">Royalty statement details below:</span>
    </div>

    <div class="bg-light p-2 shadow rounded">
        <span>Statement Period: <b>{{App\Helpers\MonthHelper::getStringMonth($fromMonth)}} {{$fromYear}}</b> to <b>{{App\Helpers\MonthHelper::getStringMonth($toMonth)}} {{$toYear}}</b></span>
        @if(count($pods) > 0)
        <table class="table table-bordered table-hover mt-2">
        <thead>
                 <tr class="text-center">
                    <th> Book Title</th>
                    <th>Format</th>
                    <th >Month</th>
                    <th>Year</th>
                    <th>Copies Sold</th>
                    <th>Retail Price</th>
                    <th>Gross Revenue</th>
                    <th>15% Royalty</th>
                </tr>
            </thead>
            <tbody style="">
                <!--
                    CHANGE LOG

                    2022-10-23:
                        - Change Grand Total from Pod Quantity to Total Pod Quantity
                            * Juncel
                -->
                @foreach ($pods as $pod)
                    @if(App\Helpers\UtilityHelper::hasTotalString($pod))
                        <tr>
                            <td colspan="4" style="border: 1px solid; width:90px; "><b>{{$pod['title']}}</b></td>
                            <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$pod['quantity']}}</b></td>
                            <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$pod['price']}}</b></td>
                            <td style="border: 1px solid; width:70px; text-align:center;">{{$pod['revenue']}}</td>
                            <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$pod['royalty']}}</b></td>
                        </tr>
                    @else
                        <tr>
                            <td style="border: 1px solid; width:230px;" >{{$pod['title']}}</td>
                            <td style="border: 1px solid; width:90px; text-align:center;">{{$pod['format']}}</td>
                            <td style="border: 1px solid; width:50px; text-align:center;">{{App\Helpers\MonthHelper::getStringMonth($pod['month'])}}</td>
                            <td style="border: 1px solid; width:50px; text-align:center;">{{$pod['year']}}</td>
                            <td style="border: 1px solid; width:70px; text-align:center;">{{$pod['quantity']}}</td>
                            <td style="border: 1px solid; width:70px; text-align:center;">{{$pod['price']}}</td>
                            <td style="border: 1px solid; width:70px; text-align:center;">{{$pod['revenue']}}</td>
                            <td style="border: 1px solid; width:70px; text-align:center;">{{$pod['royalty']}}</td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td colspan="4" style="border: 1px solid; width:90px; "><b>{{$totalPods['title']}}</b></td>
                    <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$totalPods['quantity']}}</b></td>
                    <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$totalPods['price']}}</b></td>     
        
                    <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$totalPods['revenue']}}</b></td>
                    <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$totalPods['royalty']}}</b></td>
                </tr>
            </tbody>
        </table>
        @endif
    </div>
    @if(count($ebooks) > 0)
    <div class="transaction" style="margin-top: 30px;">
        <table style="width:100%;font-size: 14px;">
            <thead style="background-color: #e3edf3;border: 1px solid;font-size: 12px;">
                <tr style="text-align:center;">
                    <th style="border: 1px solid;">eBook</th>
                    <th style="border: 1px solid;">Month</th>
                    <th style="border: 1px solid;">Year</th>
                    <th style="border: 1px solid;">Quantity</th>
                    <th style="border: 1px solid;">Retail Price</th>
                    <th style="border: 1px solid;">Author Royalty</th>
                </tr>
            </thead>
            <tbody style="">
                @foreach ($ebooks as $ebook)
                    @if(App\Helpers\UtilityHelper::hasTotalString($ebook))
                    <tr>
                        <td colspan="3" style="border: 1px solid; width:90px; "><b>{{$ebook['title']}}</b></td>
                        <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$ebook['quantity']}}</b></td>
                        <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$ebook['price']}}</b></td>
                        <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$ebook['royalty']}}</b></td>
                    </tr>
                    @else
                    <tr>
                        <td style="border: 1px solid; width:230px;" >{{$ebook['title']}}</td>
                        <td style="border: 1px solid; width:90px; text-align:center;">{{App\Helpers\MonthHelper::getStringMonth($ebook['month'])}}</td>
                        <td style="border: 1px solid; width:50px; text-align:center;">{{$ebook['year']}}</td>
                        <td style="border: 1px solid; width:50px; text-align:center;">{{$ebook['quantity']}}</td>
                        <td style="border: 1px solid; width:70px; text-align:center;">{{$ebook['price']}}</td>
                        <td style="border: 1px solid; width:70px; text-align:center;">{{$ebook['royalty']}}</td>
                    </tr>
                    @endif
                @endforeach
                <tr>
                    <td colspan="3" style="border: 1px solid; width:90px; "><b>{{$totalEbooks['title']}}</b></td>
                    <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$totalEbooks['quantity']}}</b></td>
                    <td style="border: 1px solid; width:70px; text-align:center;"></td>
                    <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$totalEbooks['royalty']}}</b></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif
    <h5 class="mt-4 my-4" style="font-size: 15px;">Total Royalties accrued as of this period: ${{$totalRoyalties}}</h5>

    
</div>

          
        </div>
    </div>
    </div>
    <script>
        // In your Javascript (external .js resource or <script> tag)
    </script>
@endsection