<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class CorrectionRequestStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'clock_in' => ['nullable', 'date_format:H:i'],
            'clock_out' => ['nullable', 'date_format:H:i'],
            'breaks' => ['nullable', 'array'],
            'breaks.*.start' => ['nullable', 'date_format:H:i'],
            'breaks.*.end' => ['nullable', 'date_format:H:i'],
            'note' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'clock_in.date_format' => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out.date_format' => '出勤時間もしくは退勤時間が不適切な値です',
            'breaks.*.start.date_format' => '休憩時間が不適切な値です',
            'breaks.*.end.date_format' => '休憩時間が不適切な値です',
            'note.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $clockIn = $this->input('clock_in');
            $clockOut = $this->input('clock_out');
            $breaks = $this->input('breaks', []);

            // 1. 出勤・退勤チェック
            if ($clockIn && $clockOut) {
                if ($clockIn > $clockOut) {
                    $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です');
                }
            }

            foreach ($breaks as $index => $break) {
                $breakStart = $break['start'] ?? null;
                $breakEnd = $break['end'] ?? null;

                // 空欄行は無視
                if (!$breakStart && !$breakEnd) {
                    continue;
                }

                // 2. 休憩開始が出勤前 / 退勤後
                if ($breakStart && $clockIn) {
                    if ($breakStart < $clockIn) {
                        $validator->errors()->add("breaks.$index.start", '休憩時間が不適切な値です');
                    }
                }

                if ($breakStart && $clockOut) {
                    if ($breakStart > $clockOut) {
                        $validator->errors()->add("breaks.$index.start", '休憩時間が不適切な値です');
                    }
                }

                // 3. 休憩終了が退勤後
                if ($breakEnd && $clockOut) {
                    if ($breakEnd > $clockOut) {
                        $validator->errors()->add("breaks.$index.end", '休憩時間もしくは退勤時間が不適切な値です');
                    }
                }
            }
        });
    }
}
