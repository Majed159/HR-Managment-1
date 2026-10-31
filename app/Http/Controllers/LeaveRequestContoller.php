<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class LeaveRequestContoller extends Controller
{
    public function index(Request $request): Response
    {
        $query = LeaveRequest::with(['employee', 'leaveType'])->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->input("status"));
        }

        return Inertia::render('leave-requests/index', [
            'leaveRequests' => $query->paginate(12)->withQueryString(),
            'employees' => Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name']),
            'leaveTypes' => LeaveType::orderBy('name')->get((['id', 'name'])),
            'filters' => $request->only(['status']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);
        $days = Carbon::parse($data['start_date'])->diffInDays(Carbon::parse($data['end_date'])) + 1;

        LeaveRequest::create([
            ...$data,
            'days' => $days,
            'status' => 'pending',
        ]);
        return back();
    }
    public function approve(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        if ($leaveRequest->status !== 'pending') {
            return back()->withErrors('status', 'Leave request has already been processed.');
        }
        DB::transaction(function () use ($request, $leaveRequest) {
            $leaveRequest->update([
                'status' => 'approve',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'review_note' => $request->input('note'),
            ]);

            $balance = LeaveBalance::firstOrCreate(
                [
                    'employee_id' => $leaveRequest->employee_id,
                    'leave_type_id' => $leaveRequest->leave_type_id,
                    'year' => (int) now()->year,
                ],
                ['entitled_days' => 0, 'used_days' => 0],
            );
            $balance->increment('used_days', $leaveRequest->days);
        });
        return back();
    }
     public function reject(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        if ($leaveRequest->status !== 'pending') {
            return back()->withErrors(['status' => 'This request has already been reviewed.']);
        }

        $leaveRequest->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_note' => $request->input('note'),
        ]);

        return back();
    }
}
