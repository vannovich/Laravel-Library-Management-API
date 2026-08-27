<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Http\Resources\MemberResource;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Member::with('activeBorrowings');

        if (request()->has('search')) {
            $search = $request->search;

            $query = $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        //
        $members = $query->paginate(10);

        return MemberResource::collection($members);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        $member = Member::create($request->validated());

        return new MemberResource($member);
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        $member->load(['activeBorrowings', 'borrowings']);


        return new MemberResource($member);
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member)
    {
        $member->update($request->validated());

        return new MemberResource($member);
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $member = Member::findOrFail($id);

            if ($member->activeBorrowings()->count() > 0) {
                return response()->json([
                    'message' => 'Cannot delete member with active borrowings'
                ], 422);
            }

            $member->delete();
            return response()->json([
                'message' => 'Member deleted with success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No data found'
            ]);
        }


        //
    }
}
