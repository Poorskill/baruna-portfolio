<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\NavigationLink;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function show(): Response
    {
        $settings = SiteSetting::whereIn('group', ['identity', 'contact'])->pluck('value', 'key')->toArray();

        return Inertia::render('Public/Contact', [
            'settings' => $settings,
            'navLinks' => NavigationLink::visible()->header()->orderBy('sort_order')->get(['id', 'label', 'url', 'is_external']),
            'socialLinks' => SocialLink::visible()->orderBy('sort_order')->get(['id', 'label', 'platform', 'url']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $key = 'contact:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'rate_limit' => "Terlalu banyak permintaan. Coba lagi dalam {$seconds} detik.",
            ])->withInput();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|min:10|max:3000',
            'honeypot' => 'prohibited',
        ]);

        RateLimiter::hit($key, 3600);

        ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
            'status' => 'unread',
            'created_at' => now(),
        ]);

        return back()->with('success', 'Pesan Anda telah terkirim. Terima kasih!');
    }
}
