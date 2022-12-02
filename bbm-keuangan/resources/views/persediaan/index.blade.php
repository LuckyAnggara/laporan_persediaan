<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Data Persediaan + Nominal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-print-css/css/bootstrap-print.min.css" media="print">
</head>

<body>
    <div class="container-fluid container-lg mx-auto mt-5">
        <span class="fs-1">Data Persediaan</span>

        <form action="{{ route('persediaan') }}" method="get">
            <div class="row my-2 d-print-none">
                <label for="staticEmail" class="col-sm-2 col-form-label">Tanggal Data</label>
                <div class="form-group col-3">
                    <div class="input-group date" id="datetimepicker">
                        <input type="text" class="form-control" name="tanggal" value="{{$tanggal}}" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="col-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            <p class="my-2">
                <span class="fs-4">Tanggal Data <span class="fw-bold">{{date('d F Y', strtotime($tanggal))}}</span></span>
            </p>
            <p>Total Semua Persediaan <span class="fw-bold "> Rp. {{number_format($totalSemuaPersediaan,0)}}</span></p>

            <div class="row d-print-none my-4">
                <label for="inputPassword" class="col-sm-2 col-form-label">Jumlah Data</label>
                <div class="col-3">
                    <select class="form-select form-select-lg" name="limit">
                        <option value="10" {{$limit == 10 ? 'selected' : ''}}>10</option>
                        <option value="50" {{$limit == 50 ? 'selected' : ''}}>50</option>
                        <option value="125" {{$limit == 125 ? 'selected' : ''}}>125</option>
                        <option value="500" {{$limit == 500 ? 'selected' : ''}}>500</option>
                        <option value="1000000" {{$limit == 1000000 ? 'selected' : ''}}>Semua</option>
                    </select>
                </div>
                <div class="col-1 ">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                <div class="col-5">
                </div>
                <div class="col-1 d-grid float-end">
                    <button type="button" class="btn btn-primary" onclick="window.print(); return false;">Print</button>
                </div>
            </div>
            {{-- TABLE --}}
            @php
            $totalTable = 0;
            @endphp
            <div>
                @if($tanggal == null)
                @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Saldo</th>
                            <th>Harga</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($persediaan->count() == 0)
                        <tr>
                            <td colspan="7">Tidak ada data</td>
                        </tr>
                        @else
                        @foreach($persediaan as $key=> $p)
                        @if ($p->balance != 0)
                        @php
                        $total = $p->harga_pokok * $p->balance;
                        $totalTable = $totalTable + $total;
                        @endphp
                        @if($total < 0) <tr style="background-color: #ed6464;">
                            @elseif($total == 0) <tr style="background-color: #ffee00;">
                                @else
                            <tr>
                                @endif
                                <td>{{ $persediaan->firstItem() + $key }}</td>
                                <td>{{ $p->kode_barang }}</td>
                                <td>{{ $p->barang->nama_barang }}</td>
                                <td>{{ $p->debit }}</td>
                                <td>{{ $p->kredit }}</td>
                                <td>{{ $p->balance }}</td>
                                <td>{{ number_format($p->harga_pokok,0) }}</td>
                                <td>{{ number_format($total,0) }}</td>
                            </tr>
                            @endif
                            @endforeach
                    <tfoot>
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="3" style="background-color: #080000;" class="text-white">TOTAL</td>
                            <td style="background-color: #080000;" class="text-white">Rp. {{number_format($totalTable,0)}}</td>
                        </tr>
                    </tfoot>

                    @endif


                    </tbody>
                </table>
                @endif
            </div>
            {{-- PAGINATION --}}

            <div class="mx-auto col-12 d-print-none">
                {{-- <ul class="pagination pagination-lg justify-content-center">
                    <li class="page-item {{$persediaan->onFirstPage() == 1 ? 'disabled' : ''}}" >
                <a class="page-link" href="{{$persediaan->previousPageUrl()}}">Previous</a>
                </li>
                <li class="page-item"><a class="page-link" href="{{$persediaan->url(10)}}">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link {{$persediaan->lastPage() == 1 ? 'disabled' : ''}}" href="{{$persediaan->nextPageUrl()}}">Next</a>
                </li>
                </ul> --}}
                {{-- <p>{{$persediaan->from()}} ke {{$persediaan->to()}} dari total {{$persediaan->total()}}</p> --}}
                {{ $persediaan->onEachSide(0)->links() }}

            </div>
        </form>
    </div>


</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
    $(function() {
        $('#datetimepicker').datetimepicker();
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>


</html>