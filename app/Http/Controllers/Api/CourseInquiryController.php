<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseInquiryController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $inquiry = CourseInquiry::create($request->all());
            
            // You can add mail notification here if needed
            
            return response()->json([
                'status' => true,
                'message' => 'Inquiry submitted successfully',
                'data' => $inquiry
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function querylistview(Request $request): JsonResponse
    {
        try {
            $inquiries = CourseInquiry::with('course')
                ->when($request->has('status'), function ($query) use ($request) {
                    return $query->where('status', $request->status);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(9);
    
            // Extract 'data' as an array for the frontend
            return response()->json([
                'success' => true,
                'inquiries' => $inquiries->items(), // Only the actual data array
                'pagination' => [
                    'current_page' => $inquiries->currentPage(),
                    'last_page' => $inquiries->lastPage(),
                    'total' => $inquiries->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch course inquiries',
                'error' => $e->getMessage(), // Include detailed error for debugging
            ], 500);
        }
    }
    

    public function updateStatusquery(Request $request, CourseInquiry $inquiry): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,contacted,closed'
            ]);

            $inquiry->update(['status' => $validated['status']]);

            return response()->json([
                'success' => true,
                'message' => 'Inquiry status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update inquiry status'
            ], 500);
        }
    }
}
