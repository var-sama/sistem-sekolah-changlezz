<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\StoreRequest;
use App\Http\Requests\Student\UpdateRequest;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Sistem Sekolah - Daftar Siswa';

        $search = $request->input('search');
        $classId = $request->query('class_id');
        $majorId = $request->query('major_id');

        $students = Student::select(['id', 'nis', 'name', 'gender', 'major_id', 'class_id', 'user_id'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%");
                });
            })
            ->when($classId, fn($query, $id) => $query->where('class_id', $id))
            ->when($majorId, fn($query, $id) => $query->where('major_id', $id))
            ->with(['schoolClass', 'major'])
            ->paginate(5)
            ->withQueryString();

        $schoolClasses = SchoolClass::all();
        $majors = Major::all();

        return view('students.index', compact('title', 'students', 'schoolClasses', 'majors'));
    }

    
    public function show(Student $student)
    {
        $title = 'Sistem Sekolah - Detail Siswa';

        $student->load(['major', 'schoolClass']);

        return view('students.show', compact('title', 'student'));
    }

    public function create()
    {
        $title = 'Sistem Sekolah - Membuat Daftar Siswa';
        $schoolClasses = SchoolClass::all();
        $majors = Major::all();

        return view('students.create', compact('title', 'schoolClasses', 'majors'));
    }

    public function edit(Student $student)
    {
        $title = 'Sistem Sekolah - Mengubah Daftar Siswa';
        $schoolClasses = SchoolClass::all();
        $majors = Major::all();

        return view('students.edit', compact('title', 'student', 'schoolClasses', 'majors'));
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        Student::create($data);

        return redirect()->route('students.index');
    }

    public function update(Student $student, UpdateRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $student->user_id ?? auth()->id();

        $student->update($data);

        return redirect()->route('students.index');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index');
    }
}