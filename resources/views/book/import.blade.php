@extends('layouts.authenticated')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-content-center mt-5">
            <div class="col-md-5">
                <a href="{{route('book.index')}}" class="btn btn-outline-primary my-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="16" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5z"/>
                      </svg>
                </a>
                <form action="{{route('book.import-bulk')}}" method="post" class="card p-4 shadow" enctype="multipart/form-data">
                    <h5 class="text-center">Import Book</h5>
                    @csrf
                    <div class="form-group my-2">
                        <label for="email">Excel File</label>
                        <input type="file" name="file" id="file" class="form-control">
                        @error('file')
                            <small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="form-group my-1">
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
