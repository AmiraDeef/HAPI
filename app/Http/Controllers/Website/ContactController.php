<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('website.contact');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);
        Contact::create($request->all());
        return response()->json(['message' => 'Thank you for your message. We will get back to you soon.']);

        //return back()->with('success', 'Thank you for your message. We will get back to you soon.');
    }
}
