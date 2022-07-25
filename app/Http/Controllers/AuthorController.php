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
        return back()->with('success', 'Successfully imported data');
    }

    public function create()
    {
        return view('author.create');
    }

    public function store(Request $request)
    {
        /**
        *   --- Task for Junior Dev ---
        *   Validate the incoming request
        *   Fields to validate { name, email, contact_number, address}
        *   ---------------------------
        */


        /**
         * Store the validated data to database
         * Use only the Model
         * ex: ModelName::create({validated data here...})
         */



         /**
          * Redirect the page to author.create
          * Add session with value of { Author successfully added to database }
          */


    }

    public function edit(Author $author)
    {
        return view('author.edit', compact('author'));
    }


    public function update(Request $request, Author $author)
    {
        /**
        *   --- Task for Junior Dev ---
        *   Validate the incoming request
        *   Fields to validate { name, email, contact_number, address}
        *   ---------------------------
        */

        /**
         * Since the author is auto binded to the Model
         * We can sure that the the author exist in the database
         * What we will is to update the existing data with the updated data
         * To achieve that use the modelVariable->update()
         */


         /**
          * Redirect the page to author.edit
          * Add session with value of { Author successfully updated to the database }
          */

    }


    public function delete(Author $author)
    {
        /**
         * You can directly delete the author
         * To achieve that, use the authorVariable->delete()
         */


         /**
          * Redirect to author.index
          * Also add session with the value of { Author has been successfully deleted from the database }
          */

    }
}
