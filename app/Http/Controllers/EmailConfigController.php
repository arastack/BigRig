<?php

namespace App\Http\Controllers;

use App\Models\EmailConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class EmailConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $emailConfig = EmailConfig::first(); // Or use specific ID if needed

        if (! $emailConfig) {
            $emailConfig = new EmailConfig(); // avoid null in view
        }

        return view('pages/settings/email-configuration', compact('emailConfig')); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailConfig $emailConfig)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailConfig $emailConfig)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailConfig $emailConfig)
    {
        $request->validate([
            'from_name'       => 'required|string|max:255',
            'from_email'      => 'required|email|max:255',
            'smtp_username'   => 'required|string|max:255',
            'smtp_password'   => 'nullable|string|max:255',
            'smtp_host'       => 'required|string|max:255',
            'smtp_port'       => 'required|integer|between:1,65535',
            'smtp_encryption' => 'nullable|string|in:ssl,tls,starttls',
        ]);

        $emailConfig = EmailConfig::first();

        if (!$emailConfig) {
            $emailConfig = new EmailConfig();
        }

        $emailConfig->from_name = $request->from_name;
        $emailConfig->from_email = $request->from_email;
        $emailConfig->smtp_username = $request->smtp_username;
        // A blank password keeps the stored one
        if (filled($request->smtp_password)) {
            $emailConfig->smtp_password = Crypt::encryptString($request->smtp_password);
        } elseif (! $emailConfig->smtp_password) {
            return redirect()->back()->withInput()->withErrors(['smtp_password' => 'Enter the SMTP password.']);
        }
        $emailConfig->smtp_host = $request->smtp_host;
        $emailConfig->smtp_port = $request->smtp_port;
        $emailConfig->smtp_encryption = $request->smtp_encryption;
        $emailConfig->save();

        return redirect()->back()->with('success', 'Email configuration updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailConfig $emailConfig)
    {
        //
    }
}
