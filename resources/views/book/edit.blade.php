@extends('layouts.authenticated')

@section('content')

<div class="container">
    <div class="container">
        <div class="row justify-content-center align-content-center mt-5">
            <div class="col-md-5">
                <a href="{{route('book.index', ['book' => $book])}}" class="btn btn-outline-primary my-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="16" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5z"/>
                      </svg>
                </a>
                <form action="" method="post" class="card p-4 shadow">
                    <h5 class="text-center">Edit Book</h5>
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <span>{{ $message }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    @csrf
                    @method('PUT')
                    <div class="form-group my-1">
                        <label for="product_id">Product ID</label>
                        <input type="number" name="product_id" id="product_id" class="form-control" placeholder="xxxx" value="{{old('product_id') ?? $book->product_id}}">
                        @error('product_id')
                            <small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="form-group my-1">
                        <label for="title">Book Title</label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="Harry Potter" value="{{old('title') ?? $book->title}}">
                        @error('title')
                            <small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>

                    <div class="form-group my-1">
                        <button type="submit" class="btn btn-primary">Update Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
