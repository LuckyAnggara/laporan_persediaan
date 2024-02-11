<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Data Pelanggan BBM Pusat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-print-css/css/bootstrap-print.min.css"
        media="print">
</head>

<body>
    <div class="container-fluid container-lg mx-auto mt-5">
        <span class="fs-1">Data Pelanggan BBM Pusat</span>

        <form action="{{ route('laporan-pelanggan') }}" method="get">

            <div class="row d-print-none my-4">
                <label class="col-sm-2 col-form-label">Jumlah Data</label>
                <div class="col-3">
                    <select class="form-select form-select-lg" name="limit">
                        <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10</option>
                        <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50</option>
                        <option value="125" {{ $limit == 125 ? 'selected' : '' }}>125</option>
                        <option value="500" {{ $limit == 500 ? 'selected' : '' }}>500</option>
                        <option value="1000000" {{ $limit == 1000000 ? 'selected' : '' }}>Semua</option>
                    </select>
                </div>
                <div class="col-1">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                <div class="col-5">
                </div>
                <div class="col-1 d-grid float-end">
                    <a  class="btn btn-primary"
                        href="{{route('export-excel-pelanggan', ['tahun' => $tahun, 'limit' => $limit])}}">Export</a>
                </div>
            </div>

            <div class="row d-print-none my-4">
                <label class="col-sm-2 col-form-label">Tahun Data</label>
                <div class="col-3">
                    <select class="form-select form-select-lg" name="tahun">
                        <option value="2023" {{ $tahun == 2023 ? 'selected' : '' }}>2023</option>
                        <option value="2022" {{ $tahun == 2022 ? 'selected' : '' }}>2022</option>
                        <option value="2021" {{ $tahun == 2021 ? 'selected' : '' }}>2021</option>
                        <option value="2020" {{ $tahun == 2020 ? 'selected' : '' }}>2020</option>
                    </select>
                </div>               
            </div>
            {{-- TABLE --}}
            @php
                $totalTable = 0;
            @endphp
            <div>
   
                    <table class="table-bordered table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th><a href="{{ route('laporan-pelanggan', ['sortby' => 'nama_pelanggan', 'sortdir' =>  $sortDir == 'asc' ?'desc' : 'asc',   'tahun' => $tahun, 'limit' => $limit]) }}"> Nama Pelanggan</a></th>
                                <th>Nomor Telepon</th>
                                <th>Alamat</th>
                                <th><a href="{{ route('laporan-pelanggan', ['sortby' => 'total', 'sortdir' =>  $sortDir == 'asc' ?'desc' : 'asc',   'tahun' => $tahun, 'limit' => $limit]) }}">Total Belanja</a></th>

                            </tr>
                        </thead>
                        <tbody>
                            @if ($pelanggan->count() == 0)
                                <tr>
                                    <td colspan="4">Tidak ada data</td>
                                </tr>
                            @else
                                @foreach ($pelanggan as $key => $p)
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        <td>{{ $p->nama_pelanggan }}</td>
                                        <td>{{ $p->nomor_telepon }}</td>
                                        <td>{{ $p->alamat }}</td>
                                        <td>{{ number_format($p->total,) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
            </div>
            {{-- PAGINATION --}}

            <div class="col-12 d-print-none mx-auto">
              
                {{ $pelanggan->onEachSide(0)->links() }}

            </div>
        </form>
    </div>


</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js">
</script>
<script type="text/javascript">
    $(function() {
        $('#datetimepicker').datetimepicker();
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
</script>


</html>
