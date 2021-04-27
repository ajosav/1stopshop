<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Helpers\ResourceHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Http\Resources\Category\CategoryResource;

class CategoryController extends Controller
{
    public function fetchParentCategories() {
        $categories = Category::whereNull('parent_id')->select('id', 'name')->get();
        return response()->success('Product categories retrieved successfully', $categories);
    }

    public function fetchSubCategories(Category $category) {
        $sub_categories = Category::whereId($category->id)->with('subCategories')->select('id', 'name')->get();
        return response()->success('Sub categories retrieved successfully', $sub_categories);
    }

    public function fetchCatWithSubs() {
        $category = Category::whereNull('parent_id')->with('subCategories')->get();
        return ResourceHelpers::categoriesWithSubs($category, "Catgories and their subcategories retrieved successfully");
    }

    public function createCategory(Request $request) {
        $category = $request->validate([
            'name' => 'required|unique:categories',
            'parent_id' => 'nullable'
        ]);

        try {
            $new_category = Category::create($category);
            if($new_category) {
                return (new CategoryResource($new_category))->additional([
                    'message' => "Catgory created successfully",
                    'status' => "success"
                ]);
            }
        } catch(QueryException $e) {
            return response()->errorResponse('Error creating category');
        }

       
    }
}
