<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Halaman manajemen kategori akun aktif.
     */
    public function index(Request $request): View
    {
        $account = $this->currentAccount($request);

        return view('categories.index', [
            'categories' => $account->categories()
                ->withCount('transactions')
                ->orderBy('type')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $account = $this->currentAccount($request);

        $attributes = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('categories', 'name')
                    ->where('account_id', $account->id)
                    ->where('type', $request->input('type')),
            ],
            'type' => ['required', Rule::in(Category::types())],
        ], [
            'name.unique' => 'Kategori dengan nama dan jenis tersebut sudah ada di akun ini.',
        ]);

        $category = $account->categories()->create([
            ...$attributes,
            'user_id' => $request->user()->id,
        ]);

        ActivityLog::record(
            $account,
            $request->user(),
            ActivityLog::CATEGORY_CREATED,
            'Membuat kategori '.$category->type.' "'.$category->name.'".',
            $category,
            ['category_name' => $category->name, 'type' => $category->type],
        );

        return redirect()->route('categories.index')->with('status', 'Kategori baru berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $account = $this->currentAccount($request);
        $this->ensureOwned($request, $category);

        $attributes = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('categories', 'name')
                    ->where('account_id', $account->id)
                    ->where('type', $request->input('type'))
                    ->ignore($category->id),
            ],
            'type' => ['required', Rule::in(Category::types())],
        ], [
            'name.unique' => 'Kategori dengan nama dan jenis tersebut sudah ada di akun ini.',
        ]);

        if ($category->transactions()->where('type', '!=', $attributes['type'])->exists()) {
            return back()->with('error', 'Jenis kategori tidak dapat diubah karena sudah dipakai transaksi.');
        }

        $oldName = $category->name;
        $oldType = $category->type;
        $category->update($attributes);

        ActivityLog::record(
            $account,
            $request->user(),
            ActivityLog::CATEGORY_UPDATED,
            'Mengubah kategori "'.$oldName.'" menjadi "'.$category->name.'".',
            $category,
            ['old_name' => $oldName, 'new_name' => $category->name, 'old_type' => $oldType, 'new_type' => $category->type],
        );

        return redirect()->route('categories.index')->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        $account = $this->currentAccount($request);
        $this->ensureOwned($request, $category);

        if ($category->transactions()->exists()) {
            return back()->with('error', 'Kategori masih dipakai transaksi dan tidak dapat dihapus.');
        }

        $name = $category->name;
        $category->delete();

        ActivityLog::record(
            $account,
            $request->user(),
            ActivityLog::CATEGORY_DELETED,
            'Menghapus kategori "'.$name.'".',
            null,
            ['category_name' => $name],
        );

        return redirect()->route('categories.index')->with('status', 'Kategori berhasil dihapus.');
    }

    private function ensureOwned(Request $request, Category $category): void
    {
        abort_unless((int) $category->account_id === (int) $this->currentAccount($request)->id, 404);
    }
}
