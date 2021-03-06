@extends('layouts.staff')
@section('title')
vZBW CMS
@stop
@section('content')
@include('includes.nav._pages')
@if($v === 'create')
	@include('staff.pages.create')
@elseif($v == 'edit')
	@include('staff.pages.edit')
@elseif($v == 'trash')
	@include('staff.pages.trash')
@elseif($v == 'menus')
    @include('staff.pages.menus.index')
@else
	@include('staff.pages.pages')
@endif
@stop
