<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('public.contact');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // For now, we just return a success message.
        // In a real application, you would send an email, save to DB, etc.
        return back()->with('success', 'Merci pour votre message ! Nous vous répondrons bientôt.');
    }
}
