@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h1 class="page-title">Overview</h1>
    
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="stat-details">
                <h3>Total Users</h3>
                <p>1,245</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <div class="stat-details">
                <h3>Total Orders</h3>
                <p>384</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fa-solid fa-envelope"></i>
            </div>
            <div class="stat-details">
                <h3>New Enquiries</h3>
                <p>28</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fa-solid fa-dollar-sign"></i>
            </div>
            <div class="stat-details">
                <h3>Payments</h3>
                <p>$12,450</p>
            </div>
        </div>
    </div>

    <!-- More Dashboard Content Can Go Here -->
    <div style="background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--border-color);">
        <h3 style="margin-bottom: 20px; font-weight: 600; font-size: 1.1rem; color: var(--text-dark);">Recent Activity</h3>
        <p style="color: var(--text-muted);">This is where graphs or a table of recent orders will go.</p>
    </div>
@endsection
