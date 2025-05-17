@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h1 class="mb-0">Contact Us</h1>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h3>Get in Touch</h3>
                            <p>
                                We'd love to hear from you! Whether you have a question about our products, 
                                your order, or anything else, our team is ready to assist you.
                            </p>
                            <div class="mt-4">
                                <h5><i class="fas fa-map-marker-alt text-primary me-2"></i> Our Location</h5>
                                <p>
                                    123 E-Commerce St.<br>
                                    Shopping City, SC 12345<br>
                                    United States
                                </p>
                            </div>
                            <div class="mt-4">
                                <h5><i class="fas fa-phone text-primary me-2"></i> Phone</h5>
                                <p>(123) 456-7890</p>
                            </div>
                            <div class="mt-4">
                                <h5><i class="fas fa-envelope text-primary me-2"></i> Email</h5>
                                <p>info@clicktobuy.com</p>
                            </div>
                            <div class="mt-4">
                                <h5><i class="fas fa-clock text-primary me-2"></i> Business Hours</h5>
                                <p>
                                    Monday - Friday: 9:00 AM - 5:00 PM<br>
                                    Saturday: 10:00 AM - 2:00 PM<br>
                                    Sunday: Closed
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="card-title mb-4">Send Us a Message</h3>
                                    <form action="{{ route('contact.submit') }}" method="POST">
                                        @csrf
                                        
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Your Name</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                            @error('email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="subject" class="form-label">Subject</label>
                                            <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required>
                                            @error('subject')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="message" class="form-label">Message</label>
                                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                                            @error('message')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">Send Message</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 