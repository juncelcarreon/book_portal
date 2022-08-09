@extends('layouts.authenticated')

@section('content')

<div class="container">
    <div class="container">
        <div class="row justify-content-center align-content-center mt-5">
            <div class="col-md-5">
                <form action="{{ route('book.store') }}" method="post" class="card p-4 shadow">
                    <a href="{{ route('book.index')}}" class="ms-auto text-decoration-none text-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-x" viewBox="0 0 16 16">
                            <path
                                d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                        </svg>
                    </a>
                    <h5 class="text-center">Add New Book</h5>
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <span>{{ $message }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    @csrf
                    <div class="form-group my-1">
                        <label for="product_id">Product ID</label>
                        <input min="0" type="number" name="product_id" id="product_id" class="form-control" placeholder="xxxx" value="{{old('product_id')}}">
                        @error('product_id')
                            <small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="form-group my-1">
                        <label for="title">Book Title</label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="Harry Potter" value="{{old('title')}}">
                        @error('title')
                            <small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>

                    <div class="form-group my-1">
                        <button type="submit" class="btn btn-primary">Add Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
