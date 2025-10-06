<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PriceEstimateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Already handled by auth middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'jenis_produk' => ['required', 'string', 'in:Pagar,Kanopi,Railing,Teralis,Pintu,Tangga'],
            'jumlah_unit' => ['required', 'integer', 'min:1'],
            'jenis_material' => ['required', 'string', 'in:hollow,besi_siku,aluminium,stainless,plat'],
            'ketebalan_mm' => ['required', 'numeric', 'min:0.1', 'max:100'],
            'finishing' => ['required', 'string', 'in:cat_biasa,cat_epoxy,powder_coating,galvanis'],
            'kerumitan_desain' => ['required', 'integer', 'in:1,2,3'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'harga_akhir' => ['sometimes', 'numeric', 'min:0'], // untuk simpan setelah calculate
        ];

        // Conditional: Jumlah lubang hanya untuk Teralis
        if ($this->input('jenis_produk') === 'Teralis') {
            $rules['jumlah_lubang'] = ['required', 'integer', 'min:1'];
        }

        // Conditional: Ukuran m² untuk non-Teralis
        if ($this->input('jenis_produk') && $this->input('jenis_produk') !== 'Teralis') {
            $rules['ukuran_m2'] = ['required', 'numeric', 'min:0.1', 'max:1000'];
        }

        // Conditional: Profile size tidak untuk plat
        if ($this->input('jenis_material') && $this->input('jenis_material') !== 'plat') {
            $rules['profile_size'] = ['required', 'string', 'max:50'];
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'jenis_produk' => 'jenis produk',
            'jumlah_unit' => 'jumlah unit',
            'jumlah_lubang' => 'jumlah lubang',
            'ukuran_m2' => 'ukuran',
            'jenis_material' => 'jenis material',
            'profile_size' => 'ukuran profile',
            'ketebalan_mm' => 'ketebalan',
            'finishing' => 'jenis finishing',
            'kerumitan_desain' => 'tingkat kerumitan desain',
            'harga_akhir' => 'harga estimasi',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'jenis_produk.required' => 'Silakan pilih jenis produk.',
            'jumlah_lubang.required' => 'Jumlah lubang wajib diisi untuk produk Teralis.',
            'ukuran_m2.required' => 'Ukuran dalam m² wajib diisi untuk produk ini.',
            'profile_size.required' => 'Ukuran profile wajib diisi untuk material ini.',
            'ketebalan_mm.max' => 'Ketebalan tidak boleh melebihi 100mm.',
            'ukuran_m2.max' => 'Ukuran tidak boleh melebihi 1000 m².',
        ];
    }
}