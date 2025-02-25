<?php

namespace App\Http\Controllers;

use App\Models\CashierUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CashierUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.cashierusers.index');
    }

    public function fetchCashierUsers(){
        $cashierUsers = CashierUsers::all();
        return response()->json([
            'cashierUsers' => $cashierUsers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'pincode' => 'required|max:10',
            'role' => 'required|in:administrator,kassza',
        ], [
            'name.required' => 'Név megadása szükséges.',
            'pincode.required' => 'Pincode megadása szükséges.',
            'role.required' => 'Jogosultság megadása szükséges: administrator / kassza',
            'role.in' => 'Nem megfelelő jogosultsági szint: administrator / kassza',
        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>400,
                'errors'=>$validator->messages(),

            ]);
        } else {
            $cashier = new CashierUsers;
            $cashier->nev = $request->input('name');
            $cashier->pinkod = $request->input('pincode');
            $cashier->jogosultsag = $request->input('role');
            $cashier->save();
            return response()->json([
                'message'=>"Kassza létrehozva",
                'status'=>200,
            ]);
        };

        

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function deleteCashierUser($id){
        $cashierUser = CashierUsers::find($id);
        $name = $cashierUser->nev;
        $cashierUser->delete();

        return response()->json([
            'message'=>"Kassza törölve: " . $name . " id: " . $id,
            'status'=>200,
        ]);
    }
}
