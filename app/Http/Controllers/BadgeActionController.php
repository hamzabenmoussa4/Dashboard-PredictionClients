<?php

namespace App\Http\Controllers;

use App\Models\BadgeActionSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BadgeActionController extends Controller
{
    public function index()
    {
        $settings = BadgeActionSetting::orderBy('badge')->get()->keyBy('badge');

        // S’assurer qu’on a une ligne pour chaque badge
        foreach (['NORMAL', 'VIP', 'RISK'] as $b) {
            if (!isset($settings[$b])) {
                BadgeActionSetting::create([
                    'badge' => $b,
                    'action_type' => 'none',
                    'message' => null,
                    'discount_percent' => null,
                ]);
            }
        }

        $settings = BadgeActionSetting::orderBy('badge')->get();

        return view('automation.actions', [
            'settings' => $settings,
        ]);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.badge' => ['required', Rule::in(['NORMAL', 'VIP', 'RISK'])],
            'items.*.action_type' => ['required', Rule::in(['none', 'email', 'discount', 'notify'])],
            'items.*.message' => ['nullable', 'string', 'max:500'],
            'items.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        foreach ($validated['items'] as $item) {
            BadgeActionSetting::where('badge', $item['badge'])->update([
                'action_type' => $item['action_type'],
                'message' => $item['message'] ?? null,
                'discount_percent' => $item['discount_percent'] ?? null,
            ]);
        }

        return redirect()
            ->route('automation.actions')
            ->with('success', 'Actions par badge enregistrées.');
    }
}
