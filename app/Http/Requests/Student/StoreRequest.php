<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nis' => ['required', 'string', 'size:4', 'unique:students,nis'],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'major_id' => ['required', 'integer', 'exists:majors,id'],
            'class_id' => ['required', 'integer', 'exists:classes,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nis' => 'Nomor Induk Siswa',
            'name' => 'Nama Lengkap',
            'gender' => 'Jenis Kelamin',
            'major_id' => 'Jurusan',
            'class_id' => 'Kelas',
        ];
    }
}
