@extends('layouts.authenticated')

@section('content')

<div class="container">
    <div class="container">
        <div class="row justify-content-center align-content-center mt-5">
            <div class="col-md-5">
                <form action="{{ route('author.store') }}" method="post" class="card p-4 shadow">
                    <a href="{{ route('author.index')}}" class="ms-auto text-decoration-none text-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-x" viewBox="0 0 16 16">
                            <path
                                d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                        </svg>
                    </a>
                    <h5 class="text-center">Add New Author</h5>
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <span>{{ $message }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    @csrf
                    <div class="form-group my-1">
                        <label for="name">Author Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="John Doe" value="{{old('name')}}">
                        @error('name')
                            <small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="form-group my-1">
                        <label for="email">Author Email</label>
                        <input type="text" name="email" id="email" class="form-control" placeholder="xxx@elink.com.ph" value="{{old('email')}}">
                        @error('email')
                            <small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="form-group my-1">
                        <label for="contact_number">Author Contact Number</label>
                        <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder="09xxxxxxxx" value="{{old('contact_number')}}">
                        @error('contact_number')
                            <small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="form-group my-1">
                        <label for="address">Author Address</label>
                        <textarea name="address" id="address" class="form-control" cols="10" rows="3">{{old('address')}}</textarea>
                        @error('address')
                            <small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="form-group my-1">
                        <button type="submit" class="btn btn-primary">Add Author</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
