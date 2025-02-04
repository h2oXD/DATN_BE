@extends('layouts.master')

@section('title')
    Danh sách người dùng
@endsection

@section('content')
    <h1>Danh sách người dùng</h1>

    @if (session()->has('success') && !session()->get('success'))
        <div class="alert alert-danger">
            {{ session()->get('error') }}
        </div>
    @endif

    @if (session()->has('success') && session()->get('success'))
        <div class="alert alert-info">
            Thao tác thành công!
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <a href="{{ route('users.create') }}" class="btn btn-primary">Thêm mới người dùng</a>

    <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Tên</th>
                    <th scope="col">Email</th>
                    <th scope="col">Số điện thoại</th>
                    <th scope="col">Vai trò</th>
                    <th scope="col">Ảnh đại diện</th>
                    <th scope="col">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone_number }}</td>
                        <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                        <td>
                            <img src="{{ Storage::url($user->profile_picture) }}" width="100px">
                        </td>
                        <td>
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-info">Xem</a>
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">Sửa</a>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{-- {{ $users->links() }} --}}
    </div>
@endsection
