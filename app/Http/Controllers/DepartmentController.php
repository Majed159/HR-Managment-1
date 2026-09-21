<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;
class DepartmentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Department::withCount([
            'positions',
            'employees',
        ])->latest();

        if ($request->filled('search')) {
            $term = "%{$request->input('search')}%";
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('code', 'like', $term);
            });
        }

        return Inertia::render('departments/index', [
            'departments' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request):RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:departrments,name',
            'code' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:255',
        ]);


        Department::create($data);
        return back();
        }


        public function update(Request $request, Department $department): RedirectResponse
        {
            $data = $request->validate([
                'name' => 'required|string|max:255|Rule::unique:departrments,name,' . $department->id,
                'code' => 'nullable|string|max:10',
                'description' => 'nullable|string|max:255',
            ]);

            $department->update($data);

            return back();
        }

        public function destroy(Department $department): RedirectResponse
        {
            $department->delete();

            return back();
        }
}
