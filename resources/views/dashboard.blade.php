@extends('layouts.authenticated')

@section('content')
<div class="container">
    <div class="container">
        <div class="row justify-content-center align-content-center mt-5">
            <div class="col-md-5">
                <form action="" method="post" class="card p-4 shadow">
                    <h5 class="text-center">Generate PDF</h5>
                    @csrf
                    <div class="form-group my-1">
                        <label for="author">Author</label>
                        <select name="author" id="author" class="form-select select2">
                            <option value="" disabled selected>Select author</option>
                            @foreach ($authors as $author)
                                <option value="{{$author->id}}">{{$author->getFullName()}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group my-1">
                        <label for="book">Book</label>
                        <select name="book" id="book" class="form-select select2">
                            <option value="" disabled selected>Select book</option>
                            @foreach ($books as $book)
                                <option value="{{$book->id}}">{{$book->title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group my-1">
                        <label for="year">Year</label>
                        <input type="text" class="form-control" name="year" id="year" value="" required>
                    </div>
                    <div class="form-group my-1">
                        <label for="month">Month</label>
                        <select name="month" id="month" class="form-select">
                            <option value="" disabled selected>Select month</option>
                        </select>
                    </div>
                    <div class="form-group my-1">
                        <button type="submit" class="btn btn-primary">Generate PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/css/datepicker.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="https://netdna.bootstrapcdn.com/bootstrap/2.3.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function(){
        $("#year").datepicker({
           format: "yyyy",
           viewMode: "years",
           minViewMode: "years",
           autoclose:true
        });

        $('.select2').select2();

        // Id of dropdown
        $('#author').change(async() => {
            //get the #book element (dropdown)
            let element = document.getElementByID('book')
            //remove existing data in dropdown (#book)
            removeOptions(element)

            //fetch data from the server base on user id
            const response = await fetch('/transaction/{author}')+$('#author').val());
            //convert response to json
            let data = await response.json()
            //understandable
            let filteredData = filterDuplicateData(data, 'book')
            //add the data to dropdoen, from the server which is the response
            createOption(element, filteredData, 'book')
        });

        const removeOptions = (element) => {
            while(element.length > 1){
                element.remove(element.length - 1)
            }
        }

        const createOptions = (element, items, type) => {
            if(items.length > 0){
                if(type != 'year'){
                    let allOpt = document.createElement('option')
                        allOpt.value = 'all'
                        allOpt.innerText = 'All'
                    element.append(allOpt)
                }

                items.forEach((item) => {
                    var opt = document.createElement('option')
                    if(type === 'book'){
                        opt.value = item.book_title
                        option.innerText = item.book_title
                    }else{
                        opt.value = item.year
                        opt.innerText = item.year
                    }
                    element.appendChild(opt)
                })
            }
        }

        const filterDuplicateData = (data, type) => {
            const uniqueItem = [];

            const unique = data.filter(element => {
                let isDuplicate = uniqueItem.includes(type === 'book' ? element.book_title : element.year);
                if(isDuplicate) {
                    uniqueItem.push(type == 'book') ? element.book_title : element.year
                    return true;
                }
                return false;
            });
            return unique;
        }
    })
</script>
@endsection
