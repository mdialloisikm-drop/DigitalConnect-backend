<?php

namespace App\Services;

use App\Http\Requests\CategoryFormRequest;
use App\Models\Category;

class CategoryService
{
    public function index()
    {
        return Category::with('projects','services')->get();
    }

    public function store(CategoryFormRequest $request)
    {
        return Category::create($request->validated());
    }

    public function show(string $id)
    {
        return Category::with('projects','services')->findOrFail($id);
    }

    public function update(array $request, string $id)
    {
        $category = $this->show($id);
        $category->update($request);
        return $category;
    }

    public function destroy(string $id)
    {
        Category::destroy($id);
    }
}
