<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthorResource;
use App\Models\Author;
use App\Http\Requests\StoreAuthorRequest;

use Illuminate\Http\Request;


class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authors = Author::with('books')->paginate(10);

        return AuthorResource::collection($authors);
        // return response()->json([
        //     'authors' => $authors,
        //     'message' => 'Authors retrieved successfully'
        // ], 200);
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorRequest $request)
    {
        $author = Author::create($request->validated());
        $author->load('books');

        return new AuthorResource($author);

        // return response()->json([
        //     'message' => 'Author created successfully',
        //     'author' => $author,
        // ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        $author->load('books');

        return new AuthorResource($author);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreAuthorRequest $request, Author $author)
    {
        $author->update($request->validated());
        $author->load('books');

        return new AuthorResource($author);
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        $author->delete();

        return response()->json([
            'message' => 'Author deleted successfully',
            'data' => $author->toArray()
        ], 200);
    }
}
