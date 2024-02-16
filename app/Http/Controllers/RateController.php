<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateArtistRequest;
use App\Models\Rate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RateController extends IController
{
    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateArtistRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Rate $rate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rate $rate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rate $rate)
    {
        //
    }
}
