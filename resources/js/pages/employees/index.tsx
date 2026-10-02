import { Head, Link, router, useForm } from "@inertiajs/react";
import { use, useState, type FormEvent } from "react";
import { toast } from "sonner";
import type {
    Department,
    Paginated,
    SearchFilters,
    Employee,
    Position,
} from "@/types/hr";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import InputError from "@/components/input-error";
import { Building2, Pencil, Plus, Search, Trash2, X } from "lucide-react";
import { Breadcrumbs } from "@/components/breadcrumbs";

interface Filters {
    search?: string;
    department?: string;
    status?: string;
}
interface Props {
    employees: Paginated<Employee>;
    departments: Pick<Department, "id" | "name">[];
    positions: Pick<Position, "id" | "title" | "department_id">[];
    managers: { id: number; first_name: string; last_name: string }[];
    filters: Filters;
}
const emptyForm = {
    first_name: "",
    last_name: "",
    email: "",
    phone: "",
    department_id: "",
    position_id: "",
    manger_id: "",
    hire_date: "",
    employment_status: "active",
    salary: "",
    address: "",
    avatar: null as File | null,
};
const statusStyles: Record<string, string> = {
    active: "bg-green-100 text-green-700",
    on_leave: "bg-amber-100 text-amver-700",
    terminated: "bg-red-100 text-red-700",
};

export default function EmployeeIndex({
    employees,
    departments,
    positions,
    managers,
    filters,
}: Props) {
    const [showCreate, setShowCreate] = useState(false);
    const [editing, setEditing] = useState<Employee | null>(null);
    const [search, setSearch] = useState(filters.search ?? "");
}
const createForm = useForm({ ...emptyForm });
const editForm = useForm({ ...emptyForm });

function submitCreate(e: FormEvent) {
    e.preventDefault();
    createForm.post("/employees", {
        forceFormData: true,
        onSuccess: () => {
            toast.success("Employee added.");
            setShowCreate(false);
            createForm.reset();
        },
    });
}

function openEdit(employee: Employee){

    setEditing(employee);
    editForm.setData({
        first_name:employee.first_name,
        last_name:employee.last_name,
        email: employee.email,
        phone:employee.phone ??'',
        department_id:employee.department_id ?  String(employee.department_id):'',
        position_id:employee.position_id ? String(employee.position_id):'',
        manger_id:employee.manager_id ? String(employee.manager_id):'',
        hire_date:employee.hire_date?.slice(0,10) ?? '',
        employment_status:employee.employment_status,
        salary:String(employee.salary),
        address:employee.address ?? '',
        avatar: null,

    });
}

function submitEdit(e: FormEvent){
    e.preventDefault();
    if(!editing)return;
    editForm.transform((data) =>({ ...data, _method:'patch'}));
    editForm.post(`/employees/${editing.id}`,{
        forceFormData:true,
        onSuccess:()=>{
            toast.success("Employee updated.");
            setEditing(null);
            editForm.reset();
        }
    })

}
function handleDelete(employee:Employee){
    if(!confirm(`Remove ${employee.full_name} from the directory?`)) return;
    router.delete(`/employees/${employee.id}`,{
        onSuccess:()=>{
            toast.success("Employee removed.");
        }
    });
}
function applyFilters(next:Filters){
    router.get('/employees',{
        search: next.search || undefined,
        department: next.department || undefined,
        status: next.status || undefined,

    },{preserveState:true,preserveScroll:true});
}
