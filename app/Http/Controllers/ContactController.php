<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactSubmission;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'company' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // Save to database
        $submission = ContactSubmission::create([
            'company' => $validated['company'],
            'email' => $validated['email'],
            'message' => 'Interested in Business System trial',
            'status' => 'new',
        ]);

        // Here you would typically send email notification
        // Mail::to('admin@example.com')->send(new NewContactSubmission($submission));
        
        return redirect('/')->with('success', 'Thank you for your interest! We will contact you soon.');
    }
}
