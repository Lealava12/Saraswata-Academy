 <form method="POST" action="{{ route('admin.verify.otp') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="otp" class="form-label">Enter 6-digit OTP</label>
                        <input type="text" 
                               class="form-control form-control-lg @error('otp') is-invalid @enderror" 
                               id="otp" 
                               name="otp" 
                               placeholder="Enter 6-digit OTP" 
                               maxlength="6"
                               pattern="[0-9]{6}"
                               required>
                        @error('otp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle me-2"></i>Verify OTP
                        </button>
                    </div>
                </form>