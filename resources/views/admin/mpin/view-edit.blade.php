@extends('admin.layouts.app')

@section('title', 'View/Edit MPIN')
@section('page-title', 'Manage Current MPIN')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white text-center py-4 border-bottom-0">
                <div class="mb-3">
                    <i class="bi bi-shield-check text-success" style="font-size: 3rem;"></i>
                </div>
                <h4 class="fw-bold mb-1">Your MPIN</h4>
                <p class="text-muted small mb-0">You can view your current MPIN or set a new one.</p>
            </div>
            <div class="card-body p-4 pt-2">
                
                {{-- View MPIN Section --}}
                <div class="mb-5 text-center bg-light p-4 rounded-3 border">
                    <label class="form-label text-uppercase text-muted small fw-bold mb-2">Current MPIN</label>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <span id="mpinDisplay" class="fs-1 fw-bolder letter-spacing-3" style="-webkit-text-security: disc;">{{ $currentMpin ?? '------' }}</span>
                        <button type="button" class="btn btn-sm btn-light border" id="toggleMpinVisibility" title="Show/Hide MPIN">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                {{-- Edit MPIN Form --}}
                <form action="{{ route('admin.manage-mpin.update') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-medium text-muted small text-uppercase">Set New MPIN</label>
                        <input type="password" name="mpin" class="form-control form-control-lg text-center letter-spacing-3 fs-3 @error('mpin') is-invalid @enderror" 
                            maxlength="6" placeholder="------" required autocomplete="new-password">
                        <div class="form-text text-center mt-2 mb-0">Must be exactly 6 digits.</div>
                        @error('mpin')
                            <div class="invalid-feedback text-center mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                        <i class="bi bi-save me-2"></i>Update MPIN
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .letter-spacing-3 {
        letter-spacing: 0.5em;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggleMpinVisibility');
        const mpinDisplay = document.getElementById('mpinDisplay');
        const icon = toggleBtn.querySelector('i');

        toggleBtn.addEventListener('click', function() {
            if (mpinDisplay.style.webkitTextSecurity === 'disc') {
                mpinDisplay.style.webkitTextSecurity = 'none';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                mpinDisplay.style.webkitTextSecurity = 'disc';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
        
        // Ensure input only accepts numbers
        const mpinInput = document.querySelector('input[name="mpin"]');
        if(mpinInput) {
            mpinInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    });
</script>
@endpush
