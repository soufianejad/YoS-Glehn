<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of all reviews.
     */
    public function index(Request $request)
    {
        $reviews = Review::with('user', 'book')->latest()->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Display a listing of pending reviews.
     */
    public function pending(Request $request)
    {
        $reviews = Review::with('user', 'book')->where('is_approved', false)->latest()->paginate(15);

        return view('admin.reviews.pending', compact('reviews'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        $review->load('user', 'book');

        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        $review->load('user', 'book');

        return view('admin.reviews.edit', compact('review'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
            'is_approved' => 'required|boolean',
        ]);

        $wasApproved = $review->is_approved;

        $review->update($validated);

        // Notify the book's author if newly approved
        if (!$wasApproved && $review->is_approved && $review->book && $review->book->author) {
            $this->notificationService->sendNotification(
                $review->book->author,
                'Nouvel avis sur votre livre',
                "Un nouvel avis a été approuvé et publié sur votre livre '{$review->book->title}'.",
                route('book.show', $review->book->slug),
                'new_review'
            );
        }

        return redirect()->route('admin.reviews.index')->with('success', __('Review updated successfully.'));
    }

    /**
     * Approve the specified review.
     */
    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        // Notify the book's author
        if ($review->book && $review->book->author) {
            $this->notificationService->sendNotification(
                $review->book->author,
                'Nouvel avis sur votre livre',
                "Un nouvel avis a été approuvé et publié sur votre livre '{$review->book->title}'.",
                route('book.show', $review->book->slug),
                'new_review'
            );
        }

        return back()->with('success', __('Review approved successfully!'));
    }

    /**
     * Reject the specified review.
     */
    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);

        return back()->with('success', __('Review rejected successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', __('Review deleted successfully.'));
    }
}
