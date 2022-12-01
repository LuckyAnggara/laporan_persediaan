<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Data Persediaan + Nominal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
</head>

<body>
    <div class="container mx-auto mt-5">
        <h3>Data Persediaan</h3>


        <form action="{{ route('persediaan') }}" method="get">
            <div class="row my-2">
                <label for="staticEmail" class="col-sm-2 col-form-label">Tanggal Data</label>
                <div class="form-group col-3">
                    <div class="input-group date" id="datetimepicker">
                        <input type="text" class="form-control" name="tanggal" value="{{$tanggal}}" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="col-3">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            <p class="my-2">
                Tanggal Data {{
            date('d F Y', strtotime($tanggal));
        }}

            </p>


            <p>Total Persediaan Rp. {{number_format($total,0)}}</p>

            <br />
            <br />
            @if($tanggal == null)
            @else
            <table class="table table-bordered">
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
                @foreach($persediaan as $key=> $p)
                @if ($p->saldo != 0)

                @php
                $total = $p->harga_pokok * $p->saldo;
                @endphp
                @if($total < 0) <tr style="background-color: #6495ED;">
                    @elseif($total == 0) <tr style="background-color: #B0E0E6;">
                        @else
                    <tr>
                        @endif

                        <td>{{ ++$key }}</td>
                        <td>{{ $p->kode_barang }}</td>
                        <td>{{ $p->barang->nama_barang }}</td>
                        <td>{{ $p->debit }}</td>
                        <td>{{ $p->kredit }}</td>
                        <td>{{ $p->saldo }}</td>
                        <td>{{ number_format($p->harga_pokok,0) }}</td>
                        <td>{{ number_format($total,0) }}</td>
                    </tr>
                    @endif
                    @endforeach

            </table>
            @endif
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