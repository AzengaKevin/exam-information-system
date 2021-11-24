@extends('layouts.base')

@section('title', 'Verify Your Email Address')

@section('body')
    <main class="">
        <div class="container">
            <div class="min-vh-100 row justify-content-center align-items-center">
                <div class="col-12 col-sm-9 col-md-6 col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-text">
                                <div class="text-center">
                                    <h1 class="text-center h3">Verify Email Address</h1>
                                    <hr>
                                    <h4 class="h6"><a href="{{ route('welcome') }}">Go Back Home</a></h4>
                                </div>
                                <x-feedback />
                                <form class="needs-validation" action="{{ route('verification.send') }}" method="post">
                                    @csrf
                                    <div>
                                        <p>You've been mailed the verification linked, open your mail and click the verification link to verify that the email is yours and not a spam</p>
                                    </div>

                                    <div class="mt-3">
                                        <button class="btn d-block w-100 btn-primary" title="Check your mail to see if the link is there first">Send Verifcation Link Again</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection