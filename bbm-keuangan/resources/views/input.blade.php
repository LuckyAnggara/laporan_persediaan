<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Input Manual</title>
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
        <span class="fs-1">Input Manual</span>

        <form action="{{ route('input') }}" method="get">
            <div class="row d-print-none my-2">
                <label for="staticEmail" class="col-sm-2 col-form-label">Bulan</label>
                <div class="col-3">
                    <select class="form-select form-select-lg" name="bulan">
                        <option value="1" {{ $bulan == 1 ? 'selected' : '' }}>JANUARI</option>
                        <option value="2" {{ $bulan == 2 ? 'selected' : '' }}>FEBRUARI</option>
                        <option value="3" {{ $bulan == 3 ? 'selected' : '' }}>MARET</option>
                        <option value="4" {{ $bulan == 4 ? 'selected' : '' }}>APRIL</option>
                        <option value="5" {{ $bulan == 5 ? 'selected' : '' }}>MEI</option>
                        <option value="6" {{ $bulan == 6 ? 'selected' : '' }}>JUNI</option>
                        <option value="7" {{ $bulan == 7 ? 'selected' : '' }}>JULI</option>
                        <option value="8"{{ $bulan == 8 ? 'selected' : '' }}>AGUSTUS</option>
                        <option value="9" {{ $bulan == 9 ? 'selected' : '' }}>SEPTEMBER</option>
                        <option value="10" {{ $bulan == 10 ? 'selected' : '' }}>OKTOBER</option>
                        <option value="11" {{ $bulan == 11 ? 'selected' : '' }}>NOVEMBER</option>
                        <option value="12" {{ $bulan == 12 ? 'selected' : '' }}>DESEMBER</option>

                    </select>
                </div>

            </div>

            <div class="row d-print-none my-2">
                <label for="staticEmail" class="col-sm-2 col-form-label">Tahun</label>
                <div class="col-3">
                    <select class="form-select form-select-lg" name="tahun">
                        <option value="2023" {{ $tahun == '2023' ? 'selected' : '' }}>2023</option>
                        <option value="2024" {{ $tahun == '2024' ? 'selected' : '' }}>2024</option>
                    </select>
                </div>
                  <div class="col-4">
                    <button type='submit' class="btn btn-primary">Submit Month</button>
                </div>
            </div>
        </form>

        <div class="row d-print-none my-2">
          
                <div class="col-4">
                    <button id='submit' type="button" class="btn btn-primary">Proses</button>
                </div>
            </div>

           


            <ul style="list-style-type:none;">
                @for ($i = 1; $i < $tanggalAkhir + 1; $i++)
                    <li><span>{{ $i }}</span> - <span id={{ $i }}> Queue </span></li>
                @endfor
            </ul>

              <div class="col-4">
                    <button id="generateMonth" type='button' class="btn btn-primary">Get Month</button>
                </div>
                
            <div >
                <span >Status Generate Month : </span> <span id='status-month' >UNPROSES </span>
            </div>



    </div>


</body>


<script src=" https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
</script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

   async function generateData()  {
        for (let i = 1; i < {{$tanggalAkhir}} + 1; i++) {
            $('#' + i).text('PROSESING');
            await $.ajax({
                type: 'GET',
                url: "{{ route('labarugi2') }}",
                data: {
                    d: i,
                    bulan: {{ $bulan }},
                    tahun: {{ $tahun }}
                },
                success: function(data) {
                    $('#' + i).text(data);
                }
            });
        }
    }

    async function generateMonth()  {
        $('#status-month').text('PROSESING')
        await $.ajax({
            type: 'GET',
            url: "{{ route('input-bulanan') }}",
            data: {
                bulan: {{ $bulan }},
                tahun: {{ $tahun }}
            },
            success: function(data) {
                $('#status-month').text('DONE')
            }
        });
    }

    $('#submit').click(function(e) {
        generateData()
    });

    $('#generateMonth').click(function(e) {
        generateMonth()
    });
</script>

</html>
