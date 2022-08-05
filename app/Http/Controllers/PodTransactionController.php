<?php

namespace App\Http\Controllers;

use App\Imports\PodTransactionsImport;
use App\Models\Author;
use Illuminate\Http\Request;
use App\Models\PodTransaction;
use Maatwebsite\Excel\Facades\Excel;

class PodTransactionController extends Controller
{
    public function index()
    {
        return view('pod.index', [
            'pod_transactions' => PodTransaction::all()
        ]);
    }

    public function create()
    {
        return view('pod.create');
    }

    public function importPage()
    {
        return view('pod.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        Excel::import(new PodTransactionsImport, $request->file('file')->store('temp'));
        return back()->with('success', 'Successfully imported data');
    }
}
