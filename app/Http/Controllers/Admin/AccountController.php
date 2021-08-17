<?php

namespace App\Http\Controllers\Admin;

use App\Models\Income;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\IncomeRequest;
use Illuminate\Database\QueryException;
use App\Http\Resources\Income\IncomeResource;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $income = Income::with('createdBy')->paginate(20);

        return IncomeResource::collection($income)->additional([
            "message"   => "Successfully retrieved income and expenses",
            "status"    => "success"
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(IncomeRequest $request)
    {
        try {
            $income = $request->user()->income()->create($request->validated());

            return (new IncomeResource($income))->additional([
                'message'       => 'Account record retrieved successfully',
                'status' => 'success'
            ]);
        } catch (QueryException $e) {
            report($e);
            return response()->errorResponse(
                "Error creating {$request->type}",
                $e->getMessage()
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function show(Income $income)
    {
        return (new IncomeResource($income))->additional([
            'message'       => 'Account record retrieved successfully',
            'status' => 'success'
        ]); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function update(IncomeRequest $request, Income $income)
    {
        try {
            if(!$income->update($request->validated())) {
                return response()->errorResponse("Update Failed");
            }

            return (new IncomeResource($income))->additional([
                'message'       => 'Account record updated successfully',
                'status' => 'success'
            ]);
        } catch (QueryException $e) {
            report($e);
            return response()->errorResponse("Update failed due to unknown error");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function destroy(Income $income)
    {
        try {
            if(!$income->delete()) {
                return response()->errorResponse("Error deleting record");
            }

            return response()->success("Account record deleted successfully");
        } catch(QueryException $e) {
            report($e);
            return response()->errorResponse("Error encountered deleting record.");
        }
    }
}
