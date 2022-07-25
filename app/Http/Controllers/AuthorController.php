<?php

namespace App\Http\Controllers;

use App\Imports\AuthorsImport;
use App\Models\Author;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class AuthorController extends Controller
{
    public function index()
    {
        return view('author.index', [
            'authors' => Author::paginate(10)
        ]);
    }

    public function importPage()
    {
        return view('author.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        Excel::import(new AuthorsImport, $request->file('file')->store('temp'));
        return back();
    }

    public function create()
    {
        return view('author.create');
    }

    public function store(Request $request)
    {
        /*
        *   --- Task for Junior Dev ---
        *   Validate the incoming request
        *   Fields to validate { name, email, contact_number, address}
        *   ---------------------------
        */
    }

}
