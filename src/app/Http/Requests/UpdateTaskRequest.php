<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->id === $this->route('task')->user_id;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['sometimes', 'in:todo,on_progress,done'],
            'priority' => ['sometimes', 'in:low,medium,high'],
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
