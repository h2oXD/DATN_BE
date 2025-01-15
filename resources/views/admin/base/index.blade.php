<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User</title>
</head>

<body>
    <h1>Index</h1>
    @php
        $columns = session()->get('columns');
        $urlbase = session()->get('urlbase');
    @endphp

    <table border="1px solid">
        <thead>
            <tr>
                @foreach ($columns as $column => $name)
                    <td>{{ $name }}</td>
                @endforeach
                <td>Thao tác</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    @foreach ($columns as $column => $name)
                        <td>
                            @if (in_array($column, FIELD_IMAGE))
                                <img src="{{ asset($item->$column) }}" width="100px" alt="">
                            @else
                                {{ $item->$column }}
                            @endif
                        </td>
                    @endforeach
                    <td>
                        <a href="{{ route($urlbase . 'show' . $item) }}">Xem</a>
                        <a href="{{ route($urlbase . 'edit' . $item) }}">Sửa</a>
                        <form action="{{ route($urlbase . 'destroy' . $item) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $data->links() }}
</body>

</html>
