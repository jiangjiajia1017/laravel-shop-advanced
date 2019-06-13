<?php
namespace APP\Http\ViewComposers;

use App\Services\CategoryService;
use Illuminate\View\View;

class CategoryTreeComposer
{

    protected $categoryService;

    public function __construct(CategoryService $service)
    {
        $this->categoryService = $service;
    }

    public function compose(View $view)
    {

     //   dd($this->categoryService->getCategoryTree());
        $view->with('categoryTree', $this->categoryService->getCategoryTree());
    }
}