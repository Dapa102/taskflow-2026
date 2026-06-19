<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'status' => ['nullable', 'in:todo,on_progress,done'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'deadline' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul tugas wajib diisi.',
            'title.max' => 'Judul tugas tidak boleh lebih dari 255 karakter.',
            'status.in' => 'Status harus berupa: todo, on_progress, atau done.',
            'priority.in' => 'Prioritas harus berupa: low, medium, atau high.',
            'deadline.date' => 'Deadline harus berupa format tanggal yang valid.',
        ];
    }
}
