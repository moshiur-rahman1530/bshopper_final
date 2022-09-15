@extends('profile.layouts.index')
@section('content')

       <div class="container-fluid bg-secondary">
            <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 100px">
                <h1 class="font-weight-semi-bold text-uppercase">My account</h1>
                <div class="d-inline-flex">
                    <p class="m-0"><a href="{{url('/')}}">Home</a></p>
                    <p class="m-0 px-2">-</p>
                    <p class="m-0">My Account</p>
                </div>
            </div>
        </div>


        <div class="home-content p-4">
        <h3><span style='color:green'>{{ucwords(Auth::user()->name)}}</span>, Welcome</h3>
        </div>


@endsection