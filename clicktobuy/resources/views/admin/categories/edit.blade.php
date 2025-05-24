@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Category: {{ $category->name }}</h1>
        <div>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Categories
            </a>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary shadow-sm ml-2">
                <i class="fas fa-plus fa-sm text-white-50"></i> Add New Category
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Category Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.update', $category->category_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_active">Status</label>
                                    <select class="form-control @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                                        <option value="1" {{ old('is_active', $category->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('is_active', $category->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="seo_title">SEO Title</label>
                            <input type="text" class="form-control @error('seo_title') is-invalid @enderror" id="seo_title" name="seo_title" value="{{ old('seo_title', $category->seo_title) }}">
                            @error('seo_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Leave empty to use category name</small>
                        </div>

                        <div class="form-group">
                            <label for="seo_description">SEO Description</label>
                            <textarea class="form-control @error('seo_description') is-invalid @enderror" id="seo_description" name="seo_description" rows="3">{{ old('seo_description', $category->seo_description) }}</textarea>
                            @error('seo_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fas fa-save mr-2"></i>Update Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Danger Zone</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.destroy', $category->category_id) }}" method="POST" class="mb-0">
                        @csrf
                        @method('DELETE')
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="text-danger mb-1">Delete this category</h5>
                                <p class="mb-0 text-muted">Once deleted, it cannot be recovered. {{ $category->products_count ? 'All associated products will become uncategorized.' : '' }}</p>
                            </div>
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this category? This action cannot be undone.')">Delete Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 