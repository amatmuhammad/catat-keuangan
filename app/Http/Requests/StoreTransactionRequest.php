<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $account = $this->account();

        return [
            'wallet_id' => [
                'required',
                Rule::exists('wallets', 'id')
                    ->where('account_id', $account->id)
                    ->where(fn ($query) => $query
                        ->where('is_shared', true)
                        ->orWhere('user_id', $this->user()->id)),
            ],
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where('account_id', $account->id),
            ],
            'type' => ['required', Rule::in(Category::types())],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999999.99'],
            'description' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
        ];
    }

    /**
     * Pastikan kategori yang dipilih sesuai dengan jenis transaksi.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty() || ! $this->category_id) {
                    return;
                }

                $matches = $this->account()->categories()
                    ->whereKey($this->category_id)
                    ->where('type', $this->type)
                    ->exists();

                if (! $matches) {
                    $validator->errors()->add('category_id', 'Kategori tidak sesuai dengan jenis transaksi.');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'wallet_id.required' => 'Pilih dompet terlebih dahulu.',
            'wallet_id.exists' => 'Dompet yang dipilih tidak valid.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'type.required' => 'Jenis transaksi wajib dipilih.',
            'type.in' => 'Jenis transaksi harus pemasukan atau pengeluaran.',
            'amount.required' => 'Nominal transaksi wajib diisi.',
            'amount.numeric' => 'Nominal transaksi harus berupa angka.',
            'amount.min' => 'Nominal transaksi minimal Rp 0,01.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
        ];
    }

    private function account(): Account
    {
        /** @var Account|null $account */
        $account = $this->attributes->get('currentAccount');

        return $account ?? $this->user()->currentAccount;
    }
}
