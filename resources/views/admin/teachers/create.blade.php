@extends('admin.layouts.app')
@section('title', 'Add Teacher')
@section('page-title', 'Add Teacher')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-plus-fill me-2"></i>Add Teacher</span>
        <a href="{{ route('admin.teachers.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.teachers.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Mobile <span class="text-danger">*</span></label>
                    <input type="number" 
                        name="mobile" 
                        class="form-control" 
                        value="{{ old('mobile') }}" 
                        oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);" 
                        required>                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Joining Date <span class="text-danger">*</span></label>
                    <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Subjects</label>
                    <div class="row g-2">
                        @foreach($subjects as $s)
                        <div class="col-md-3 col-sm-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="subjects[]" id="subj{{ $s->id }}" value="{{ $s->id }}"
                                    {{ in_array($s->id, old('subjects', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="subj{{ $s->id }}">{{ $s->name }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-2"></i>Save</button>
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
