@extends('admin.layouts.app')
@section('content')
    <!-- OverView Start -->
    @include('admin.dashboard.overview')
    <!-- OverView End -->

    <!-- Recent Projects Start -->
    @include('admin.dashboard.recent_projects')
    <!-- Recent Sales End -->


    <!-- Widgets Start -->
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <!-- Email List Widget -->
            @include('admin.dashboard.email-list')
            <!-- Email List Widget End -->

            <div class="col-sm-12 col-md-6 col-xl-4">
                <div class="h-100 bg-secondary rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Calender</h6>
                    </div>
                    <div id="calender"></div>
                </div>
            </div>
            <!-- Todo List Widget -->
            <div class="col-sm-12 col-md-6 col-xl-4">
                @include('admin.dashboard.todo')
            </div>
            <!-- Todo List Widget End -->
        </div>
    </div>
    <!-- Widgets End -->
@endsection
