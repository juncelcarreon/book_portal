@extends('layouts.authenticated')

@section('content')
<div class="container">
    <div class="row justify-content-center align-content-center mt-5">
        <div class="col-md-5">
            <a href="{{route('author.index')}}" class="btn btn-outline-primary my-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="16" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5z"/>
                  </svg>
            </a>
            <form action="" method="post" class="card p-4 shadow">
                <h5 class="text-center">Update Author</h5>
                @csrf
                @method('PUT')
                <div class="form-group my-1">
                    <label for="name">Author Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="John Doe" value="{{old('name') ?? $author->name}}">
                    @error('name')
                        <small class="text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="form-group my-1">
                    <label for="email">Author Email</label>
                    <input type="text" name="email" id="email" class="form-control" placeholder="xxx@elink.com.ph" value="{{old('email') ?? $author->email}}">
                    @error('email')
                        <small class="text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="form-group my-1">
                    <label for="contact_number">Author Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder="09xxxxxxxx" value="{{old('contact_number') ?? $author->contact_number}}">
                    @error('contact_number')
                        <small class="text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="form-group my-1">
                    <label for="address">Author Address</label>
                    <textarea name="address" id="address" class="form-control" cols="10" rows="3">{{old('address') ?? $author->address}}</textarea>
                    @error('address')
                        <small class="text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="form-group my-1">
                    <button type="submit" class="btn btn-primary">Update Author</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
