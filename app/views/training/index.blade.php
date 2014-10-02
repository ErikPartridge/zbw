@extends('layouts.training')
@section('title')
Your Training
@stop
@section('header')
	@yield('header')
@stop
@section('content')
    <div class="col-md-12">
        <h2 class="text-center">Your Training Progress</h2>
        <div id="training" class="progress progress-striped active">
            <div class="progress-bar" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100" style="width:{{ $progress }}%"></div>
        </div>
        <h5>Current VATSIM Rating: <span class="sans">{{ $me->rating->long }}</span></h5>
        @if($me->cert > 0)
        <h5>Current ZBW Certification: <span class="sans">{{ $me->certification->readable() }}</span></h5>
        @else
        <h5>Current ZBW Certification: <span class="sans">N/A</span></h5>
        @endif
    </div>
    <div class="col-md-6">
        @if($me->cert < 11)
            @if($me->cert == 2 && $me->rating_id < 2)
              <form class="axform" action="/me/request/vatusa" method="post">
                <button type="submit" class="btn btn-primary">Request VATUSA S1 Exam</button>
              </form>
            @elseif($me->cert == 5 && $me->rating_id < 3)
            <form class="axform" action="/me/request/vatusa" method="post">
                <button type="submit" class="btn btn-primary">Request VATUSA S2 Exam</button>
            </form>
            @elseif($me->cert == 8 && $me->rating_id < 4)
            <form class="axform" action="/me/request/vatusa" method="post">
                <button type="submit" class="btn btn-primary">Request VATUSA S3 Exam</button>
            </form>
            @elseif($me->cert == 10 && $me->rating_id < 5)
            <form class="axform" action="/me/request/vatusa" method="post">
                <button type="submit" class="btn btn-primary">Request VATUSA C1 Exam</button>
            </form>
        @endif
        <a class="btn btn-primary" href="/training/request/new">Request Training</a>
        {{--@if($me->canTakeNextExam())--}}
        @if(in_array($me->cert_id, [0,1,2,4,5,7,8,10]))
          <a class="btn btn-primary" href="/training/local-exam">Request {{ $me->certification->nextReadable()  }} Exam</a>
        @endif
        @if($me->hasReviews())
            @if(false)
            <a class="btn btn-primary" href="/training/review">Review Exams</a>
            @endif
        @endif
        @else
        <h5>Congratulations! You have already completed all ZBW training and are a fully certed center!</h5>
        @endif
    </div>
@stop
