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

        <form action="{{ route('persediaan') }}" method="get">
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
            @php
            $totalTable = 0;
            @endphp
            <div class="row">
                @if($tanggal1 == null)
                @else
                <div class="col-10">
                    <table class="table">
                        <thead>
                            <th style="width:5%">No</th>
                            <th style="width:70%">Account</th>
                            <th style="width:25%">Base ({{date('d F Y', strtotime($tanggal1))}})</th>
                        </thead>
                        <tbody>
                            <tr>
                                <!-- // PENJUALAN -->
                                <td>1</td>
                                <td>PENJUALAN</td>
                                <td>Rp. {{number_format($data->total_penjualan)}}</td>
                            </tr>
                            <tr>
                                <!-- // TOTAL DISKON -->
                                <td>2</td>
                                <td>DISKON</td>
                                <td class="text-danger">Rp. {{number_format($data->diskon)}}</td>
                            </tr>
                            <tr>
                                <!-- // TOTAL RETUR -->
                                <td>3</td>
                                <td>RETUR</td>
                                <td class="text-danger">Rp. {{number_format($data->retur_total)}}</td>
                            </tr>

                            <tr>
                                <!-- // TOTAL PENJUALAN -->
                                <td>4</td>
                                <td class="fw-bold">TOTAL PENJUALAN (1-2-3)</td>
                                <td class="fw-bold">Rp. {{number_format($data->grand_total)}}</td>
                            </tr>

                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <!-- // PERSEDIAAN AWAL -->
                                <td>5</td>
                                <td>PERSEDIAAN AWAL</td>
                                <td>Rp. {{number_format($data->persediaan_awal)}}</td>
                            </tr>

                            <tr>
                                <!-- // PEMBELIAN -->
                                <td>6</td>
                                <td>PEMBELIAN</td>
                                <td>Rp. {{number_format($data->total_pembelian)}}</td>
                            </tr>

                            <tr>
                                <!-- // PERSEDIAAN AKHIR -->
                                <td>7</td>
                                <td>PERSEDIAAN AKHIR</td>
                                <td class="text-danger">Rp. {{number_format($data->persediaan_akhir)}}</td>
                            </tr>

                            <tr>
                                <!-- // HARGA POKOK PENJUALAN -->
                                <td>8</td>
                                <td class="fw-bold">HARGA POKOK PENJUALAN (5+6-7)</td>
                                <td class="fw-bold">Rp. {{number_format($data->hpp)}}</td>
                            </tr>

                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <!-- // TOTAL PENDAPATAN DARI PENJUALAN -->
                                <td>9</td>
                                <td class="fw-bold">TOTAL PENDAPATAN (4-8)</td>
                                <td class="fw-bold {{$data->pendapatan < 0 ? 'text-danger': '' }}">Rp. {{number_format($data->pendapatan)}}</td>
                            </tr>

                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <!-- // BEBAN -->
                                <td></td>
                                <td class="fw-bold">BEBAN</td>
                                <td></td>
                            </tr>
                            @php
                            $total_biaya = 0;
                            @endphp
                            @foreach($data->biaya as $key=> $biaya)
                            @php
                            $total_biaya = $total_biaya + $biaya->total;
                            @endphp
                            <!-- <tr>
                                <td class="text-right">-</td>
                                <td class="fw-lighter">{{$biaya->nama->nama_biaya}}</td>
                                <td class="fw-lighter">Rp. {{number_format($biaya->total)}}</td>
                            </tr> -->
                            @endforeach

                            <tr>
                                <!-- // BEBAN OPERASIONAL -->
                                <td>10</td>
                                <td class="fw-bold">TOTAL BEBAN OPERASIONAL</td>
                                <td class="fw-bold text-danger">Rp. {{number_format($total_biaya)}}</td>
                            </tr>

                            <tr>
                                <!-- // BEBAN GAJI -->
                                <td>11</td>
                                <td class="fw-bold">TOTAL BEBAN GAJI</td>
                                <td class="fw-bold text-danger">Rp. {{number_format($data->gaji)}}</td>
                            </tr>

                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <!-- // TOTAL LABA / RUGI -->
                                <td>12</td>
                                <td class="fw-bold">LABA / RUGI (9-10-11)</td>
                                <td class="fw-bold {{$data->pendapatan - $total_biaya - $data->gaji < 0 ? 'text-danger': '' }}">Rp. {{number_format($data->pendapatan - $total_biaya - $data->gaji)}} </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div class="col-2">
                    <table class="table">
                        <thead>
                            <th>{{date('d F Y', strtotime($tanggal2))}}</th>
                        </thead>
                        <tbody>
                            <tr>
                                <!-- // PENJUALAN -->
                                <td>Rp. {{number_format($data->total_penjualan)}}</td>
                            </tr>
                            <tr>
                                <!-- // TOTAL DISKON -->
                                <td class="text-danger">Rp. {{number_format($data->diskon)}}</td>
                            </tr>
                            <tr>
                                <!-- // TOTAL RETUR -->
                                <td class="text-danger">Rp. {{number_format($data->retur_total)}}</td>
                            </tr>

                            <tr>
                                <!-- // TOTAL PENJUALAN -->
                                <td class="fw-bold">Rp. {{number_format($data->grand_total)}}</td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <!-- // PERSEDIAAN AWAL -->
                                <td>Rp. {{number_format($data->persediaan_awal)}}</td>
                            </tr>

                            <tr>
                                <!-- // PEMBELIAN -->
                                <td>Rp. {{number_format($data->total_pembelian)}}</td>
                            </tr>

                            <tr>
                                <!-- // PERSEDIAAN AKHIR -->
                                <td class="text-danger">Rp. {{number_format($data->persediaan_akhir)}}</td>
                            </tr>

                            <tr>
                                <!-- // HARGA POKOK PENJUALAN -->
                                <td class="fw-bold">Rp. {{number_format($data->hpp)}}</td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <!-- // TOTAL PENDAPATAN DARI PENJUALAN -->
                                <td class="fw-bold {{$data->pendapatan < 0 ? 'text-danger': '' }}">Rp. {{number_format($data->pendapatan)}}</td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>
                            <tr>
                                <!-- // BEBAN OPERASIONAL -->
                                <td><span class="text-white">beban</span></td>
                            </tr>
                            @php
                            $total_biaya = 0;
                            @endphp
                            @foreach($data->biaya as $key=> $biaya)
                            @php
                            $total_biaya = $total_biaya + $biaya->total;
                            @endphp
                            <!-- <tr>
                                <td class="fw-lighter">Rp. {{number_format($biaya->total)}}</td>
                            </tr> -->
                            @endforeach

                            <tr>
                                <!-- // BEBAN OPERASIONAL -->
                                <td class="fw-bold text-danger">Rp. {{number_format($total_biaya)}}</td>
                            </tr>

                            <tr>
                                <!-- // BEBAN GAJI -->
                                <td class="fw-bold text-danger">Rp. {{number_format($data->gaji)}}</td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <!-- // TOTAL LABA / RUGI -->
                                <td class="fw-bold {{$data->pendapatan - $total_biaya - $data->gaji < 0 ? 'text-danger': '' }}">Rp. {{number_format($data->pendapatan - $total_biaya - $data->gaji)}} </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                @endif
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