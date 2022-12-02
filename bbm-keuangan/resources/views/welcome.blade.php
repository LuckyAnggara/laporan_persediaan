<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keuangan BBM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>

    <div class="container">
        <h1>Berkah Baja Makmur</h1>
        <h3>Keuangan</h3>

        <a type="button" class="btn btn-primary mt-2" href="{{ route('persediaan') }}">Persediaan</a>
        <a type="button" class="btn btn-primary mt-2" href="{{ route('labarugi')}}">Laporan Laba / Rugi Harian</a>
        <a type="button" class="btn btn-primary mt-2" href="{{ route('labarugibulanan')}}">Laporan Laba / Rugi Bulanan</a>
        <a type="button" class="btn btn-primary mt-2" href="{{ route('labarugi')}}">Laporan Laba / Rugi Tahun</a>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>