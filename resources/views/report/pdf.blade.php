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
        <h6 style="font-size: 20px;"><b>June Vic W. Cadayona</b></h6>
        <span style="font-size: 15px;">Tres De Abril, Labangon, Cebu City</span>
    </div>
    <div class="transaction" style="margin-top: 30px;">
        <span>Statement Period: <b>(From)</b> to <b>(End)</b></span>
        <table style="width:100%;font-size: 14px;">
            <thead style="background-color: #e3edf3;border: 1px solid;font-size: 12px;">
                <tr style="text-align:center;">
                    <th style="border: 1px solid;">Print</th>
                    <th style="border: 1px solid;">Format</th>
                    <th style="border: 1px solid;">Month</th>
                    <th style="border: 1px solid;">Year</th>
                    <th style="border: 1px solid;">Copies Sold</th>
                    <th style="border: 1px solid;">Retail Price</th>
                    <th style="border: 1px solid;">15% Royalty</th>
                </tr>
            </thead>
            <tbody style="">
                @for ($x = 1; $x <= 20; $x++)
                    <tr>
                        <td style="border: 1px solid; width:230px;" >Sample Title {{$x}}</td>
                        <td style="border: 1px solid; width:90px; text-align:center;">Patchbook</td>
                        <td style="border: 1px solid; width:50px; text-align:center;">January</td>
                        <td style="border: 1px solid; width:50px; text-align:center;">2022</td>
                        <td style="border: 1px solid; width:70px; text-align:center;">20</td>
                        <td style="border: 1px solid; width:70px; text-align:center;">$2.99</td>
                        <td style="border: 1px solid; width:70px; text-align:center;">$1.99</td>
                    </tr>
                @endfor

            </tbody>
        </table>
    </div>
</div>

@endsection
