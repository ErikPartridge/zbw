<h1 class="text-center">Roster Admin</h1>
<div class="col-md-8">
    <h3>Current Roster</h3>
    <table class="full table-bordered">
        <thead>
        <th>Name</th>
        <th>Email</th>
        <th>CID</th>
        <th>Rating</th>
        <th>Edit</th>
        <th>Training</th>
        </thead>
        @foreach($users as $u)
        @if($u->is_ta || $u->is_atm || $u->is_datm || $u->is_webmaster)
        <tr class="danger">
        @elseif($u->is_instructor)
        <tr class="warning">
        @elseif($u->is_staff || $u->is_mentor)
        <tr class="info">
        @elseif($u->artcc !== 'ZBW')
        <tr class="success">
        @else
        <tr>
        @endif
            <td><a href="/controllers/{{$u->cid}}">{{ $u->username }}</a></td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->cid }}</td>
            <td>{{ $u->rating->short }} {{ $u->is_mentor }}</td>
            <td><a class="btn btn-sm" href="/staff/{{$u->cid}}/edit">Edit</a></td>
            <td><a class="btn btn-sm" href="#">Training</a></td>
        </tr>
        @endforeach
    </table>
</div>
<div class="col-md-4">
    <h3>Search Controllers</h3>
    @include('includes.search._controller')
</div>
