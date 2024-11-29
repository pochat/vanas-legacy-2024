<?php

# Libreria de funciones
require("modules/common/lib/cam_general.inc.php");



$Query = "SELECT  DATE_FORMAT(fe_counter, '%M %e, %Y'),round1,round2,round3,fe_start_date from c_configuracion WHERE cl_configuracion=171 ";
$row = RecuperaValor($Query);
$start_date_count = str_texto($row[0]); //"May 30, 2023";
$round1 = str_texto($row[1]);
$round2 = str_texto($row[2]);
$round3 = str_texto($row[3]);
$fe_start_date= str_texto($row[4]);




#Recovery templates.
$Queryt = "SELECT ds_encabezado_it,ds_cuerpo_it FROM k_template_doc WHERE fl_template=222 ";
$rowt = RecuperaValor($Queryt);
$ds_encabezado = html_entity_decode($rowt[0]);
$ds_cuerpo = html_entity_decode($rowt[1]);


$ds_template = $ds_encabezado . $ds_cuerpo;

$ds_template = str_replace("#round1#", $round1, $ds_template);
$ds_template = str_replace("#round2#", $round2, $ds_template);
$ds_template = str_replace("#round3#", $round3, $ds_template);
$ds_template = str_replace("#start_date#", $fe_start_date, $ds_template);


?>


<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />

    <title>Vanas</title>
    <style>
        .js-clock {
            justify-content: center;
            align-items: center;
            display: flex;
        }
        .box-2 {
            max-width: 112px;
            max-height: 88px;
            background-color: #0162c9;
            border-radius: 10px;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 5px;
            
            padding: 10px;
            font-family: Impact,Haettenschweiler,Franklin Gothic Bold,Charcoal,sans-serif;
            font-size: 12px;
            line-height: 20px;
            display: flex;
        }
        .clock-number-2 {
            color: #fff;
            font-family: Inter,sans-serif;
            font-size: 50px;
            font-weight: 700;
            line-height: 50px;
        }
        .clock-label-2 {
            color: #ebebeb;
            letter-spacing: 5px;
            text-transform: uppercase;
            margin-right: -5px;
            font-family: Lato,sans-serif;
        }


    </style>

</head>
<body style="background: transparent;">

    <br /><br />



    <div class="row">
        <div class="col-md-12">
            <div id="js-clock" class="row js-clock">
                <div class="col-md-3 box-2">
                    <div id="js-clock-days" class="clock-number-2">00</div><div class="clock-label-2">Giorni</div>
                </div>
                <div class="col-md-3 box-2">
                    <div id="js-clock-hours" class="clock-number-2">00</div><div class="clock-label-2">Ore</div>
                </div>
                <div class="col-md-3 box-2">
                    <div id="js-clock-minutes" class="clock-number-2">00</div><div class="clock-label-2">Minuti</div>
                </div>
                <div class="col-md-3 box-2">
                    <div id="js-clock-seconds" class="clock-number-2">00</div><div class="clock-label-2">Secondi</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8 text-center">

            <?php echo $ds_template;?>

        </div>
        <div class="col-md-2"></div>
    </div>


    <script>
(function () {

    var deadline = '<?php echo $start_date_count; ?>';

    function pad(num, size) {
        var s = "0" + num;
        return s.substr(s.length - size);
    }

    // fixes "Date.parse(date)" on safari
    function parseDate(date) {
        const parsed = Date.parse(date);
        if (!isNaN(parsed)) return parsed
        return Date.parse(date.replace(/-/g, '/').replace(/[a-z]+/gi, ' '));
    }

    function getTimeRemaining(endtime) {
        let total = parseDate(endtime) - Date.parse(new Date())
        let seconds = Math.floor((total / 1000) % 60)
        let minutes = Math.floor((total / 1000 / 60) % 60)
        let hours = Math.floor((total / (1000 * 60 * 60)) % 24)
        let days = Math.floor(total / (1000 * 60 * 60 * 24))

        return { total, days, hours, minutes, seconds };
    }

    function clock(id, endtime) {
        let days = document.getElementById(id + '-days')
        let hours = document.getElementById(id + '-hours')
        let minutes = document.getElementById(id + '-minutes')
        let seconds = document.getElementById(id + '-seconds')

        var timeinterval = setInterval(function () {
            var time = getTimeRemaining(endtime);

            if (time.total <= 0) {
                clearInterval(timeinterval);
            } else {
                days.innerHTML = pad(time.days, 2);
                hours.innerHTML = pad(time.hours, 2);
                minutes.innerHTML = pad(time.minutes, 2);
                seconds.innerHTML = pad(time.seconds, 2);
            }
        }, 1000);
    }

    clock('js-clock', deadline);
})();
    </script>




    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>