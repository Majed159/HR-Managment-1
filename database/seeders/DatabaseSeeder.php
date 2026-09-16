<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Payslip;
use App\Models\Position;
use App\Models\User;
use App\ModelsAttendance;
use App\ModelsDepartment;
use App\ModelsLeaveBalance;
use App\ModelsPayslip;
use AppModelsLeaveRequest;
use AppModelsLeaveType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

use function Laravel\Prompts\title;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $admin = User::factory()->create([
            'name' => 'Alex Admin',
            'email' => 'admin@hr.test',
            'role' => 'admin',
        ]);
        $hr = User::factory()->create([
            'name' => 'Hana HR',
            'email' => 'hr@hr.test',
            'role' => 'hr',
        ]);
        $managerUser = User::factory()->create([
            'name' => 'Mia Manager',
            'email' => 'manager@hr.test',
            'role' => 'manager',
        ]);
        $employeeUser = User::factory()->create([
            'name' => 'Evan Employee',
            'email' => 'employee@hr.test',
            'role' => 'employee',

        ]);

        //-Leave types
        $annual = LeaveType::create(['name' => 'Annual Leave', 'default_days_per_year' => 20, 'is_paid' => true]);
        $sick = LeaveType::create(['name' => 'Sick Leave', 'default_days_per_year' => 10, 'is_paid' => true]);
        $unpaid = LeaveType::create(['name' => 'Unpaid Leave', 'default_days_per_year' => 0, 'is_paid' => false]);
        $leaveTypes = [$annual, $sick, $unpaid];


        $blueprint =[

            'departments' => [
                ['name' => 'Human Resources', 'description' => 'Handles recruitment, employee relations, and training.', 'code' => 'HR'],
                ['name' => 'Finance', 'description' => 'Manages company finances, budgeting, and payroll.', 'code' => 'FIN'],
                ['name' => 'IT', 'description' => 'Responsible for technology infrastructure and support.', 'code' => 'IT'],
                ['name' => 'Sales', 'description' => 'Drives revenue through customer acquisition and retention.', 'code' => 'SAL'],
                ['name' => 'Marketing', 'description' => 'Promotes the company and its products/services.', 'code' => 'MKT'],
            ],
            // Add more entities as needed
            'positions' => [
                ['name' => 'HR Specialist', 'description' => 'Handles HR tasks and employee relations.'],
                ['name' => 'Finance Analyst', 'description' => 'Analyzes financial data and prepares reports.'],
                ['name' => 'IT Support Technician', 'description' => 'Provides technical support and troubleshooting.'],
                ['name' => 'Sales Representative', 'description' => 'Sells products/services to customers.'],
                ['name' => 'Marketing Coordinator', 'description' => 'Assists in marketing campaigns and promotions.'],
            ],
        ];
        $positions = collect();
        $departments = collect();
        foreach ($blueprint as $deptName =>$titles)
        {
            foreach ($titles as $title)
            {
                $department = Department::create($title);
                $departments->push($department);

                // Create positions for each department
                $position = Position::create([
                    'name' => $title['name'] . ' Specialist',
                    'description' => 'Position in the ' . $title['name'] . ' department.',
                    'department_id' => $department->id,
                ]);
                $positions->push($position);
            }
        }

        // A few mangers  first so staff can report to them

        $mangers = collect();
        foreach ($departments as $i =>$department)
            {
                $mangerPosition = $department->positions()->where('name', 'like', '%Manager%')->first() ?? $positions->where('department_id', $department->id)->first();
                $mangers->push(Employee::factory()->create([
                    'user_id' => User::factory()->create([
                        'name' => fake()->name(),
                        'email' => fake()->unique()->safeEmail(),
                        'role' => 'manager',
                    ])->id,
                    'department_id' => $department->id,
                    'position_id' => $mangerPosition->id,
                    'manager_id' => null, // Managers don't have a manager
                ]));



                }


        Employee::factory()->create([
            'user_id'=>$employeeUser->id,
            'first_name' =>"Majd",
            'last_name'=>'Employee',
            'email' =>'employee@hr.test',
            'department_id'=>$departments->first()->id,
            'position_id'=>$positions->first()->id,
            'manager_id'=>$mangers->first()->id,
        ]);

        for ($i=0; $i < 30; $i++) {
            $department = $departments->random();
            $position = $positions->where('department_id', $department->id)->random();

            Employee::factory()->create([
                'department_id' => $department->id,
                'position_id' => $position->id,
                'manager_id' => $mangers->where('department_id', $department->id)->random()->id,
            ]);
        }


            $year = (int) now()->year;
        Employee::all()->each(function (Employee $employee) use ($leaveTypes,$hr, $year) {
            foreach ($leaveTypes as $leaveType) {
                LeaveBalance::create([
                    'employee_id' => $employee->id,
                    'leave_type_id' => $leaveType->id,
                    'year' => $year,
                    'entitled_days' => $leaveType->default_days_per_year,
                    'used_days'=>0,
                ]);
            }
            if (rand(1,10) <=4){
                $type = $leaveTypes[array_rand($leaveTypes)];
                $start = Carbon::now()->addDays(rand(-20, 20));
                $end = (clone $start)->addDays(rand(0, 4));
                $status = ['pending', 'approved', 'rejected'][array_rand(['pending', 'approved', 'rejected'])];


                LeaveRequest::create([

                    'employee_id' => $employee->id,
                    'leave_type_id' => $type->id,
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                    'days'=>$start->diffInDays($end)+1,
                    'reason' =>'Personal time off.',

                    'status' => $status,
                    'reviewed_at' => $status !== 'pending' ? Carbon::now() : null,
                    'reviewed_by' => $status !== 'pending' ? $hr->id : null,
                ]);
            }

            //Attendance records for the last 5 working days
            for ($d=0; $d <=5 ; $d++) {
            $date = Carbon::now()->subDays($d);
            $in = (clone $date)->setTime(rand(8, 10), rand(0, 59));
            Attendance::create([
                    'employee_id' => $employee->id,
                    'work_date' => $date->toDateString(),
                    'clock_in' => $in->toDateTimeString(),
                    'clock_out' => (clone $in)->addHours(rand(7, 9)),
                    'status'=>(int) $in->format('H') >= 9 ? 'late' : 'on_time',


            ]);
            }
            //This month's payslip
            $gross = (float) $employee->salary/12;
            $deductions = round($gross * 0.2, 2); // 10% deductions
            Payslip::create([
                'employee_id'=>$employee->id,
                'period_start'=>now()->startOfMonth()->toDateString(),
                'period_end'=>now()->endOfMonth()->toDateString(),
                'gross_pay'=>round($gross, 2),
                'deductions'=>$deductions,
                'net_pay'=>round($gross - $deductions, 2),
                'issued_at'=>now(),
            ]);

        });
    }
}
