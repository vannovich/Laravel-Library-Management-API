<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrowing;
use App\Http\Resources\BorrowingResource;
use App\Http\Requests\StoreBorrowingsRequest;
use App\Models\Book;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Borrowing::with(['book', 'member']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by member
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        $borrowings = $query->latest()->paginate(15);

        return BorrowingResource::collection($borrowings);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBorrowingsRequest $request)
    {

        $book = Book::findOrFail($request->book_id);

        // Check if book is available
        if (!$book->isAvailable()) {
            return response()->json([
                'message' => 'This book is currently unavailable.'
            ], 422);
        }

        // Create borrowing record
        $borrowing = Borrowing::create([
            'book_id' => $request->book_id,
            'member_id' => $request->member_id,
            'borrowed_date' => $request->borrowed_date,
            'due_date' => $request->due_date,
            'status' => 'borrowed',
        ]);

        $book->borrow();

        // Load relationships for the resource
        $borrowing->load(['book', 'member']);

        return new BorrowingResource($borrowing);
    }


    /**
     * Display the specified resource.
     */
    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['book', 'member']);

        return new BorrowingResource($borrowing);
        //
    }

    public function returnBook(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'borrowed') {
            return response()->json([
                'message' => 'Book has already been returned'
            ], 422);
        }

        $borrowing->update([
            'returned_date' => now(),
            'status' => 'returned'
        ]);

        $borrowing->book->returnBook();

        $borrowing->load(['book', 'member']);
        return new BorrowingResource($borrowing);
    }

    public function overdue()
    {
        $overdueBorrowings = Borrowing::with(['book', 'member'])
            ->where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->get();

        Borrowing::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        return BorrowingResource::collection($overdueBorrowings);
    }
}
