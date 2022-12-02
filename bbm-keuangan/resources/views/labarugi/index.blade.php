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

        <form action="{{ route('labarugi') }}" method="get">
            <div class="row my-2 d-print-none">
                <label for="staticEmail" class="col-sm-2 col-form-label">Tanggal Data</label>
                <div class="form-group col-3">
                    <div class="input-group date" id="datetimepicker">
                        <input type="text" class="form-control" name="tanggal" value="{{$tanggal1}}" />
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
                <span class="fs-4">Tanggal Data <span class="fw-bold">{{date('d F Y', strtotime($tanggal1))}}</span></span>
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
                @if($tanggal1 == null)
                @else
                <div class="col-9">
                    <table class="table">
                        <thead>
                            <th style="width:5%">No</th>
                            <th style="width:70%">Account</th>
                            <th style="width:25%">Base ({{date('d F Y', strtotime($tanggal1))}})</th>
                        </thead>
                        <tbody>
                            @foreach($data1 as $key=> $d)
                            <tr>
                                <!-- // PENJUALAN -->
                                <td>{{$d->no}}</td>
                                <td class="{{$d->class}}">{{$d->account}}</td>
                                <td class="text-right {{$d->class}}">Rp. {{number_format($d->balance)}}</td>
                            </tr>
                            @if($d->no == 3)
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @elseif($d->no == 7)
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @elseif($d->no == 8)
                            <tr>
                                <td></td>
                                <td>BEBAN</td>
                                <td></td>
                            </tr>
                            @elseif($d->no == 10)
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
                            <th>({{date('d F Y', strtotime($tanggal2))}})</th>
                        </thead>
                        <tbody>
                            @foreach($data2 as $key=> $d)
                            <tr>
                                <!-- // PENJUALAN -->
                                <td class="text-right {{$d->class}}">Rp. {{number_format($d->balance)}}</td>
                            </tr>
                            @if($d->no == 3)
                            <tr>
                                <td></td>
                            </tr>
                            @elseif($d->no == 7)
                            <tr>
                                <td></td>
                            </tr>
                            @elseif($d->no == 8)
                            <tr>
                                <td class="text-white">BEBAN</td>
                            </tr>
                            @elseif($d->no == 10)
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