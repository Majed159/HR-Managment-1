<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

class PositionController extends Controller
{
    public function index(Request $request):Response
    {
        $query = Position::withcount('department')->latest();

        if ($request->filled('search')) {

            $query->where('title', 'like', "%{$request->input('search')}%");
     };
     if ($request->filled('department_id')) {
        $query->where('department_id', $request->integer('department'));

     }
        return inertia('positions/index', [
            'positions' => $query->paginate(10)->withQueryString(),
            'departments' => Department::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['search','department']),
        ]);
    }
    public function store(Request $request,Position $position) : RedirectResponse
    {
        $data = $request->validate([
            'department_id' => ['required','exists:departments,id'],
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
        ]);

        Position::create($data);

        return Redirect::route('positions.index');

    }
    public function update(Request $request, Position $position): RedirectResponse
    {
        $data = $request->validate([
            'department_id' => ['required','exists:departments,id'],
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
        ]);

        $position->update($data);

        return Redirect::route('positions.index');
    }
    public function destroy(Position $position): RedirectResponse
    {
        $position->delete();

        return Redirect::route('positions.index');
    }
}
