@extends('layouts.authenticated')

@section('content')
<div class="container ">
    <div class="p-3 my-3 w-100 ">
        <div class="d-flex">
            <div class="ms-auto my-2">
                <a href="{{route('author.import-page')}}" class="btn btn-outline-primary">Bulk Import</a>
                <a href="{{route('author.create')}}" class="btn btn-outline-success">Add Author</a>
            </div>
        </div>
        <div class="bg-light p-2 shadow rounded">
            <table class="table table-bordered table-hover mt-2">
                <thead>
                    <tr class="text-center">
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact Number</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($authors as $author)
                    <tr>
                        <td>{{$author->name}}</td>
                        <td>{{$author->email}}</td>
                        <td>{{$author->contact_number}}</td>
                        <td>{{$author->address}}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-warning">Edit</button>
                                <button class="btn btn-outline-danger">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">No Author found in database</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-2">
            {{$authors->links()}}
        </div>
    </div>

</div>
@endsection
