@extends('layouts.app')

@section('content')
<style>
    body{
        background: #ffffff;
    }
</style>

<div class="upper" style="">
    <div class="image-container">
        <img src="https://readersmagnet.com/wp-content/uploads/2019/08/ReadersMagnet-Favicon.png" height="150px" width="150px" alt="Readers Magnet Image" srcset="">
    </div>
    <div class="detail-container" style="position:absolute; right:0; top:40px;">
        <b style="position: relative; bottom: 10px;">Readers Magnet</b>
        <br>
        <a href="info@readersmagnet.com" style="position: relative; bottom: 10px;">info@readersmagnet.com</a>
        <p>(800) 805-0762</p>
    </div>
</div>
<div id="lower">
    <div class="title" style="text-align: center;">
        <h6 style="font-size: 30px;">Royalty Statement</h6>
    </div>
    <div class="details" style="margin-top: 30px;">
        <span>{{$currentDate}}</span>
        <h6 class="mt-4" style="font-size: 15px;"><b>{{$author->getFullName()}}</b></h6>
        <span style="font-size: 15px;">{{$author->address}}</span>
    </div>
    <div class="details" style="margin-top: 30px;">
        <span style="font-size: 15px; mb-5;">Dear {{$author->firstname}},</span>
        <br><br>
        <span style="font-size: 15px;">Enclosed is the royalty payment amounting to ${{$totalRoyalties}} ({{$numberFormatter}}).</span>
        <br>
        <span  style="font-size: 15px;">Royalty statement details below:</span>
    </div>

    <div class="transaction" style="margin-top: 30px;">
        <span>Statement Period: <b>{{App\Helpers\MonthHelper::getStringMonth($fromMonth)}} {{$fromYear}}</b> to <b>{{App\Helpers\MonthHelper::getStringMonth($toMonth)}} {{$toYear}}</b></span>
        @if(count($pods) > 0)
        <table style="width:100%;font-size: 14px;">
            <thead style="background-color: #e3edf3;border: 1px solid;font-size: 12px;">
                <tr style="text-align:center;">
                    <th style="border: 1px solid;">Book Title</th>
                    <th style="border: 1px solid;">Format</th>
                    <th style="border: 1px solid;">Month</th>
                    <th style="border: 1px solid;">Year</th>
                    <th style="border: 1px solid;">Copies Sold</th>
                    <th style="border: 1px solid;">Retail Price</th>
                    <th style="border: 1px solid;">15% Royalty</th>
                </tr>
            </thead>
            <tbody style="">
                @foreach ($pods as $pod)
                    <tr>
                        <td style="border: 1px solid; width:230px;" >{{$pod['title']}}</td>
                        <td style="border: 1px solid; width:90px; text-align:center;">{{$pod['format']}}</td>
                        <td style="border: 1px solid; width:50px; text-align:center;">{{App\Helpers\MonthHelper::getStringMonth($pod['month'])}}</td>
                        <td style="border: 1px solid; width:50px; text-align:center;">{{$pod['year']}}</td>
                        <td style="border: 1px solid; width:70px; text-align:center;">{{$pod['quantity']}}</td>
                        <td style="border: 1px solid; width:70px; text-align:center;">{{$pod['price']}}</td>
                        <td style="border: 1px solid; width:70px; text-align:center;">{{$pod['royalty']}}</td>
                    </tr>

                @endforeach

                    <tr>
                        <td colspan="4" style="border: 1px solid; width:90px; "><b>Grand Total</b></td>
                        <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$totalPOD['quantity']}}</b></td>
                        <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$totalPOD['price']}}</b></td>
                        <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$totalPOD['royalty']}}</b></td>
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
                    <tr>
                        <td style="border: 1px solid; width:230px;" >{{$ebook['title']}}</td>
                        <td style="border: 1px solid; width:90px; text-align:center;">{{App\Helpers\MonthHelper::getStringMonth($ebook['month'])}}</td>
                        <td style="border: 1px solid; width:50px; text-align:center;">{{$ebook['year']}}</td>
                        <td style="border: 1px solid; width:50px; text-align:center;">{{$ebook['quantity']}}</td>
                        <td style="border: 1px solid; width:70px; text-align:center;">{{$ebook['price']}}</td>
                        <td style="border: 1px solid; width:70px; text-align:center;">{{$ebook['royalty']}}</td>
                    </tr>
                    @endforeach

                <tr>
                    <td colspan="3" style="border: 1px solid; width:90px; "><b>Grand Total</b></td>
                    <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$totalEbook['quantity']}}</b></td>
                    <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$totalEbook['price']}}</b></td>
                    <td style="border: 1px solid; width:70px; text-align:center;"><b>{{$totalEbook['royalty']}}</b></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif
    <h5 class="mt-4 my-4" style="font-size: 15px;">Total Royalties accrued as of this period: ${{$totalRoyalties}}</h5>

    <span style="font-size: 15px;">Sincerely,</span>
    <h5 style="font-size: 15px;"><b>ReadersMagnet Team</b></h5>
</div>

@endsection
