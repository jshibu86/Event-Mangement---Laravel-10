<?php

namespace App\Http\Controllers;

use App\Models\Contributor;
use Illuminate\Http\Request;

class ContributorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->trim()->toString();

        $contributors = Contributor::with('media') // avoids one query per row
            ->when(
                $search,
                fn ($q) => $q->where(
                    fn ($x) => $x->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                )
            )
            ->latest()
            ->get();

        return view('contributors.index', compact('contributors', 'search'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'        => 'required|max:255',
            'email'       => 'nullable|email|max:255',
            'phone'       => 'nullable|max:50',
            'description' => 'nullable',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $contributor = Contributor::create(collect($data)->except('photo')->all());

        if ($r->hasFile('photo')) {
            $contributor->addMediaFromRequest('photo')->toMediaCollection('photo');
        }

        return back()->with('success', 'Contributor added.');
    }

    public function update(Request $r, Contributor $contributor)
    {
        $data = $r->validate([
            'name'         => 'required|max:255',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|max:50',
            'description'  => 'nullable',
            'photo'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_photo' => 'nullable|boolean',
        ]);

        $contributor->update(collect($data)->except(['photo', 'remove_photo'])->all());

        if ($r->hasFile('photo')) {
            $contributor->addMediaFromRequest('photo')->toMediaCollection('photo');
        } elseif ($r->boolean('remove_photo')) {
            $contributor->clearMediaCollection('photo');
        }

        return back()->with('success', 'Contributor updated.');
    }

    public function toggle(Contributor $contributor)
    {
        $contributor->update([
            'status' => !$contributor->status,
        ]);

        return response()->json([
            'ok'     => true,
            'status' => $contributor->status,
        ]);
    }
}
