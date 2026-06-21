<?php

namespace Webkraft\Cms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Webkraft\Cms\Support\Branding;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        // Honeypot — bots fill hidden fields.
        if (filled($request->input('company'))) {
            return back()->with('wk_contact_sent', true);
        }

        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $to = Branding::contactEmail();

        if ($to) {
            Mail::raw(
                "Navn: {$data['name']}\nE-mail: {$data['email']}\n\n{$data['message']}",
                fn ($m) => $m->to($to)
                    ->replyTo($data['email'], $data['name'])
                    ->subject('Ny henvendelse via '.config('app.name'))
            );
        }

        return back()->with('wk_contact_sent', true);
    }
}
