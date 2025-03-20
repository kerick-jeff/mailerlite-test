<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Jobs\CreateSubscriber;

class SubscriberController extends Controller
{
    public function store(Request $request)
    {
        // Validate required fields
        $request->validate([
            'email' => 'required|email|unique:subscribers,email',
            'status' => 'required|in:subscribed,unsubscribed'
        ]);

        // Queue the creation for high traffic
        CreateSubscriber::dispatch($request->all());

        return response()->json(['message' => 'Subscriber created successfully'], 202);
    }
}
