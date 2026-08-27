<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Resources\BookResource;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::with('author.books');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('isbn', 'like', '%' . $search . '%')
                    ->orWhereHas('author', function ($authorQuery) use ($search) {
                        $authorQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if( $request->filled('genre') ) {
            $query->where('genre', $request->genre);
        }

        $books = $query->paginate(10);

        return BookResource::collection($books);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $data = $request->validated();

        $data['available_copies'] = $data['total_copies'];
        $data['status'] = 'available';

        $book = Book::create($data);

        $book->load('author.books');

        return new BookResource($book);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $book = Book::with('author.books')->findOrFail($id);

            return new BookResource($book);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'The Book is not found'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $data = $request->validated();

        $book->update($data);

        $book->load('author.books');

        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json([
            'message' => "Successfully deleted book",
            'data' => $book
        ]);
        //
    }
}
