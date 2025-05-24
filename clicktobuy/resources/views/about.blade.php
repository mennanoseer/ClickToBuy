@extends('layouts.app')

@section('title', 'About Us')

@section('styles')
<style>
    .about-banner {
        background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://via.placeholder.com/1920x400');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 80px 0;
        margin-bottom: 40px;
    }
    
    .team-member {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .team-member img {
        border-radius: 50%;
        margin-bottom: 20px;
        width: 150px;
        height: 150px;
        object-fit: cover;
    }
    
    .timeline {
        position: relative;
        padding: 0;
        list-style: none;
    }
    
    .timeline:before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 50%;
        width: 2px;
        margin-left: -1px;
        background-color: #e9ecef;
    }
    
    .timeline>li {
        position: relative;
        margin-bottom: 50px;
        min-height: 50px;
    }
    
    .timeline>li:after, .timeline>li:before {
        content: " ";
        display: table;
    }
    
    .timeline>li:after {
        clear: both;
    }
    
    .timeline>li .timeline-panel {
        float: left;
        position: relative;
        width: 46%;
        padding: 20px;
        border: 1px solid #d4d4d4;
        border-radius: 8px;
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.05);
        background-color: #fff;
    }
    
    .timeline>li .timeline-panel:before {
        content: " ";
        display: inline-block;
        position: absolute;
        top: 26px;
        right: -15px;
        border-top: 15px solid transparent;
        border-right: 0 solid #ccc;
        border-bottom: 15px solid transparent;
        border-left: 15px solid #ccc;
    }
    
    .timeline>li .timeline-panel:after {
        content: " ";
        display: inline-block;
        position: absolute;
        top: 27px;
        right: -14px;
        border-top: 14px solid transparent;
        border-right: 0 solid #fff;
        border-bottom: 14px solid transparent;
        border-left: 14px solid #fff;
    }
    
    .timeline>li .timeline-image {
        position: absolute;
        z-index: 100;
        left: 50%;
        width: 80px;
        height: 80px;
        margin-left: -40px;
        text-align: center;
        color: white;
        border: 7px solid #e9ecef;
        border-radius: 100%;
        background-color: #007bff;
    }
    
    .timeline>li .timeline-image h4 {
        font-size: 16px;
        line-height: 14px;
        margin-top: 12px;
    }
    
    .timeline>li.timeline-inverted>.timeline-panel {
        float: right;
    }
    
    .timeline>li.timeline-inverted>.timeline-panel:before {
        right: auto;
        left: -15px;
        border-right-width: 15px;
        border-left-width: 0;
    }
    
    .timeline>li.timeline-inverted>.timeline-panel:after {
        right: auto;
        left: -14px;
        border-right-width: 14px;
        border-left-width: 0;
    }
    
    @media (max-width: 767px) {
        .timeline:before {
            left: 40px;
        }
        .timeline>li .timeline-image {
            left: 40px;
            margin-left: 0;
            width: 60px;
            height: 60px;
        }
        .timeline>li .timeline-panel {
            width: calc(100% - 90px);
            width: -moz-calc(100% - 90px);
            width: -webkit-calc(100% - 90px);
            float: right;
        }
        .timeline>li .timeline-panel:before {
            right: auto;
            left: -15px;
            border-right-width: 15px;
            border-left-width: 0;
        }
        .timeline>li .timeline-panel:after {
            right: auto;
            left: -14px;
            border-right-width: 14px;
            border-left-width: 0;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h1 class="mb-0">About ClickToBuy</h1>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <img src="https://via.placeholder.com/600x400?text=ClickToBuy+Store" alt="Our Store" class="img-fluid rounded">
                        </div>
                        <div class="col-md-6">
                            <h2>Our Story</h2>
                            <p>
                                ClickToBuy was founded in 2023 with a simple mission: to make online shopping easier, 
                                more accessible, and more enjoyable for everyone. What started as a small venture has 
                                grown into a comprehensive e-commerce platform offering thousands of products across 
                                multiple categories.
                            </p>
                            <p>
                                We believe in putting customers first and ensuring that every shopping experience is 
                                smooth, secure, and satisfying. Our dedicated team works tirelessly to curate the best 
                                products at competitive prices, deliver them quickly, and provide excellent customer service.
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h2>Our Values</h2>
                            <div class="row mt-3">
                                <div class="col-md-4 text-center mb-4">
                                    <div class="bg-light p-3 h-100 rounded">
                                        <i class="fas fa-thumbs-up fa-3x mb-3 text-primary"></i>
                                        <h4>Quality</h4>
                                        <p>We carefully select every product to ensure it meets our high standards of quality and value.</p>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center mb-4">
                                    <div class="bg-light p-3 h-100 rounded">
                                        <i class="fas fa-user-shield fa-3x mb-3 text-primary"></i>
                                        <h4>Trust</h4>
                                        <p>We build trust through transparent business practices and secure transactions.</p>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center mb-4">
                                    <div class="bg-light p-3 h-100 rounded">
                                        <i class="fas fa-headset fa-3x mb-3 text-primary"></i>
                                        <h4>Service</h4>
                                        <p>Customer satisfaction is our priority, with responsive support and hassle-free returns.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h2>Our Team</h2>
                            <p>
                                Behind ClickToBuy is a diverse team of passionate professionals with expertise in e-commerce, 
                                technology, logistics, and customer service. We're united by our commitment to innovation and 
                                our drive to create the best online shopping platform possible.
                            </p>
                            <p>
                                Our leadership team brings decades of combined experience in retail and e-commerce, 
                                guiding our growth with a clear vision and strategic approach to the evolving digital marketplace.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
