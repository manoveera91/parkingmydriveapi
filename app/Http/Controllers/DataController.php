<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string',
            'value' => 'required|string',
        ]);

        // Store the data in the session or a database as needed
        session([$data['key'] => $data['value']]);

        return response()->json(['message' => 'Data stored successfully']);
    }

    public function retrieve(Request $request)
    {
        $key = $request->query('key');

        // Retrieve the data from the session or a database as needed
        $value = session($key);

        return response()->json(['value' => $value]);
    }
}