<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Laporan Laba Rugi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-print-css/css/bootstrap-print.min.css" media="print">
</head>

<body>
    <div class="container-fluid container-lg mx-auto mt-5">
        <span class="fs-1">Laporan Laba Rugi</span>

        <form action="{{ route('labarugibulanan') }}" method="get">
        <div class="row my-2 d-print-none">
                <label for="staticEmail" class="col-sm-2 col-form-label">Bulanan</label>
                <div class="col-3">
                <select class="form-select form-select-lg" name="bulan">
                    <option value="1" {{$bulan == 1 ? 'selected' : ''}}>JANUARI</option>
                    <option value="2" {{$bulan == 2 ? 'selected' : ''}}>FEBRUARI</option>
                    <option value="3" {{$bulan == 3 ? 'selected' : ''}}>MARET</option>
                    <option value="4" {{$bulan == 4 ? 'selected' : ''}}>APRIL</option>
                    <option value="5" {{$bulan == 5 ? 'selected' : ''}}>MEI</option>
                    <option value="6" {{$bulan == 6 ? 'selected' : ''}}>JUNI</option>
                    <option value="7" {{$bulan == 7 ? 'selected' : ''}}>JULI</option>
                    <option value="8"{{$bulan == 8? 'selected' : ''}}>AGUSTUS</option>
                    <option value="9" {{$bulan == 9 ? 'selected' : ''}}>SEPTEMBER</option>
                    <option value="10" {{$bulan == 10? 'selected' : ''}}>OKTOBER</option>
                    <option value="11" {{$bulan == 11 ? 'selected' : ''}}>NOVEMBER</option>
                    <option value="12" {{$bulan == 12? 'selected' : ''}}>DESEMBER</option>

                </select>
                </div>
                <div class="col-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>

        
            <p class="my-2">
                <span class="fs-4">Bulan Data <span class="fw-bold">{{$bulan1}}</span></span>
            </p>

            <div class="row d-print-none my-4">
                <div class="col-11">

                </div>
                <div class="col-1 d-grid float-end">
                    <button type="button" class="btn btn-primary" onclick="window.print(); return false;">Print</button>
                </div>
            </div>
            {{-- TABLE --}}
            <div class="row">
                @if($bulan1 == null)
                @else
                <div class="col-9">
                    <table class="table">
                        <thead>
                            <th style="width:5%">No</th>
                            <th style="width:70%">Account</th>
                            <th style="width:25%">Base ({{$bulan1 }})</th>
                        </thead>
                        <tbody>
                            @foreach($data1 as $key=> $d)
                            <tr>
                                <!-- // PENJUALAN -->
                                <td>{{$d->nomor}}</td>
                                <td class="{{$d->class}}">{{$d->account}}</td>
                                <td class="text-right {{$d->class}}">Rp. {{number_format($d->balance)}}</td>
                            </tr>
                            @if($d->nomor == 3)
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @elseif($d->nomor == 7)
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @elseif($d->nomor == 8)
                            <tr>
                                <td></td>
                                <td>BEBAN</td>
                                <td></td>
                            </tr>
                            @elseif($d->nomor == 10)
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-3">
                    <table class="table">
                        <thead>
                            <th>({{date('F', strtotime($lastMonth)) .' '. date('Y') }})</th>
                        </thead>
                        <tbody>
                            @foreach($data2 as $key=> $d)
                            <tr>
                                <!-- // PENJUALAN -->
                                <td class="text-right {{$d->class}}">Rp. {{number_format($d->balance)}}</td>
                            </tr>
                            @if($d->nomor == 3)
                            <tr>
                                <td></td>
                            </tr>
                            @elseif($d->nomor == 7)
                            <tr>
                                <td></td>
                            </tr>
                            @elseif($d->nomor == 8)
                            <tr>
                                <td class="text-white">BEBAN</td>
                            </tr>
                            @elseif($d->nomor == 10)
                            <tr>
                                <td></td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </form>
    </div>


</body>

<script src=" https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js">
</script>
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