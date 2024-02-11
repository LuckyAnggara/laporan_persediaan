<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Laporan Laba Rugi Tahunan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-print-css/css/bootstrap-print.min.css" media="print">
</head>

<body>
    <div class="container-fluid container-lg mx-auto mt-5">
        <span class="fs-1">Laporan Laba Rugi</span>

        <form action="{{ route('labarugitahunan') }}" method="get">
        <div class="row my-2 d-print-none">
                <label for="staticEmail" class="col-sm-2 col-form-label">Tahun</label>
                <div class="col-3">
                <select class="form-select form-select-lg" name="tahun">
                    <option value="2022" {{$tahun == 2022 ? 'selected' : ''}}>2022</option>
                    <option value="2023" {{$tahun == 2023 ? 'selected' : ''}}>2023</option>
                    <option value="2024" {{$tahun == 2024 ? 'selected' : ''}}>2024</option>
                </select>
                </div>

                <div class="col-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                
            </div>
        
            <p class="my-2">
                <span class="fs-4">Tahun Data <span class="fw-bold">{{$tahun}}</span></span>
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
         
                <div class="col-12">
                    <table class="table">
                        <thead>
                            <th style="width:5%">No</th>
                            <th style="width:70%">Account</th>result
                            <th style="width:25%">{{$tahun }}</th>
                        </thead>
                        <tbody>
                            @foreach($data as $key=> $d)
                            <tr>
                                <!-- // PENJUALAN -->
                                <td>{{$d["nomor"]}}</td>
                                <td class="{{$d["class"]}}">{{$d["account"]}}</td>
                                <td class="text-right {{$d["class"]}}">Rp. {{number_format($d["balance"])}}</td>
                            </tr>
                            @if($d["nomor"] == 3)
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @elseif($d["nomor"] == 7)
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @elseif($d["nomor"] == 8)
                            <tr>
                                <td></td>
                                <td>BEBAN</td>
                                <td></td>
                            </tr>
                            @elseif($d["nomor"] == 10)
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