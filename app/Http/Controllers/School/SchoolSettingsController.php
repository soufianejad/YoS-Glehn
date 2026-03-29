<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SchoolSettingsController extends Controller
{
    public function settings()
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', __('You are not associated with a school.'));
        }

        return view('school.settings.index', compact('school'));
    }

    public function updateSettings(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', __('You are not associated with a school.'));
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('schools')->ignore($school->id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('schools')->ignore($school->id),
            ],
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'primary_color' => ['nullable', 'string', 'max:7', 'starts_with:#'],
            'students_can_view_classmates' => 'boolean',
        ]);

        $schoolData = $request->except(['logo', 'banner_image']);
        $schoolData['students_can_view_classmates'] = $request->boolean('students_can_view_classmates');

        if ($request->hasFile('logo')) {
            $uploadedLogo = $request->file('logo');
            if ($uploadedLogo && $uploadedLogo->isValid()) {
                if ($school->logo) {
                    Storage::disk('public')->delete($school->logo);
                }
                $path = 'schools/logos';
                $name = Str::random(40).'.'.$uploadedLogo->getClientOriginalExtension();
                Storage::disk('public')->put($path.'/'.$name, file_get_contents($uploadedLogo));
                $schoolData['logo'] = $path.'/'.$name;
            }
        }

        if ($request->hasFile('banner_image')) {
            $uploadedBanner = $request->file('banner_image');
            if ($uploadedBanner && $uploadedBanner->isValid()) {
                if ($school->banner_image) {
                    Storage::disk('public')->delete($school->banner_image);
                }
                $path = 'schools/banners';
                $name = Str::random(40).'.'.$uploadedBanner->getClientOriginalExtension();
                Storage::disk('public')->put($path.'/'.$name, file_get_contents($uploadedBanner));
                $schoolData['banner_image'] = $path.'/'.$name;
            } else {
                Log::warning('Uploaded banner_image file is not valid or could not be retrieved.', ['file_info' => $uploadedBanner]);
            }
        }

        $school->update($schoolData);

        return back()->with('success', __('School settings updated successfully.'));
    }

    public function regenerateAccessCode()
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', __('You are not associated with a school.'));
        }

        $school->access_code = strtoupper(Str::random(8));
        $school->save();

        return back()->with('success', __('Access code regenerated successfully.'));
    }
}
