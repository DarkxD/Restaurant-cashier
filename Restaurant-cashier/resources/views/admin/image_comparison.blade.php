@extends('admin.admin_layout')

@section('title', "Képek összehasonlítása")

@section('content')

<div class="container py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Képek összehasonlítása</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Kép</th>
                                <th>Fájlnév</th>
                                <th>Létezik az adatbázisban?</th>
                                <th>Műveletek</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comparison as $row)
                                <tr>
                                    <td>
                                        <img src="{{ asset('storage/images/' . $row['filename']) }}" alt="Kép" style="width: 50px; height: 50px;">
                                    </td>
                                    <td>{{ $row['filename'] }}</td>
                                    <td>{{ $row['exists_in_database'] }}</td>
                                    <td>
                                        @if($row['exists_in_database'] === 'Nem')
                                            <form action="{{ route('delete.image', ['filename' => $row['filename']]) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Biztosan törölni szeretnéd ezt a képet?')">Törlés</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection