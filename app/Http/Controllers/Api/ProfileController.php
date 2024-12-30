<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;


class ProfileController extends Controller
{
    public function getProfile()
    {
        $user = Auth::user();
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'contact_details' => [
                'phone' => $user->phone,
                'address' => $user->address,
                'city' => $user->city,
                'state' => $user->state,
                'postcode' => $user->postcode
            ],
            'profile_info' => [
                'profile_picture' => $user->profile_picture,
                'bio' => $user->bio
            ],
            'teacher_details' =>in_array($user->role, ['teacher', 'admin']) ? [
                'occupation' => $user->occupation,
                'company_name' => $user->company_name,
                'expertise' => $user->teacher_expertise,
                'social_links' => [
                    'linkedin' => $user->linkedin,
                    'facebook' => $user->facebook,
                    'twitter' => $user->twitter,
                    'instagram' => $user->instagram
                ],
                'teacher_request_status' => $user->teacher_request_status
            ] : null
        ]);
    }
    
    /**
     * Update user profile
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|min:4',
            'bio' => 'nullable|string|max:500',
            'linkedin' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255'
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }            
            
            $picturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validatedData['profile_picture'] = $picturePath;
        }
        // Update user with teacher request details
        /** @var \App\Models\User $user */
        $user->update($validatedData);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }
}
