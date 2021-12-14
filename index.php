<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="css/slick.css" />
  <link rel="stylesheet" href="css/jquery.fancybox.css" />
</head>

<body>
  <style>
    .calendar-item {
      width: 200px;
      display: inline-block;
      vertical-align: top;

      font: 14px/1.2 Arial, sans-serif;
    }

    .calendar-head {
      text-align: center;
      padding: 5px;
      font-weight: 600;
      font-size: 14px;
      text-transform: uppercase;
    }

    .calendar-item table {
      border-collapse: collapse;
      width: 100%;
    }

    .calendar-item th {
      font-size: 12px;
      padding: 6px 7px;
      text-align: center;
      color: #888;
      font-weight: normal;
    }

    .calendar-item td {
    font-size: 16px;
    padding: 5px 5px;
    text-align: center;
    border: 1px solid #fff;
}

    .calendar-day.last {
      color: #999 !important;
    }

    .calendar-day.today {
      font-weight: bold;
    }

    .calendar-day.event {
      background: #88b6d0;
      color: #fff;
      position: relative;
      cursor: pointer;
    }

    .calendar-day.event:hover .calendar-popup {
      display: block;
    }

    .calendar-popup {
      display: none;
      position: absolute;
      top: 40px;
      left: 0;
      min-width: 200px;
      padding: 15px;
      background: #fff;
      text-align: left;
      font-size: 13px;
      z-index: 100;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
      color: #000;
    }

    .calendar-popup:before {
      content: "";
      border: solid transparent;
      position: absolute;
      left: 8px;
      bottom: 100%;
      border-bottom-color: #fff;
      border-width: 9px;
      margin-left: 0;
    }

    .weekend {
      background-color: tomato;
    }

    .empty {
      display: none;
    }

    .calendar-wrp .slick-list {
      overflow: initial;
    }

  </style>

  <?php
  class Calendar
  {

    public static function  getMonth($month, $year, $events = array())
    {
      $months = array(
        1  => 'Январь',
        2  => 'Февраль',
        3  => 'Март',
        4  => 'Апрель',
        5  => 'Май',
        6  => 'Июнь',
        7  => 'Июль',
        8  => 'Август',
        9  => 'Сентябрь',
        10 => 'Октябрь',
        11 => 'Ноябрь',
        12 => 'Декабрь'
      );

      $month = intval($month);
      $out = '
		<div class="calendar-item">
			
			<table>';

      $day_week = date('N', mktime(0, 0, 0, $month, 1, $year));
      $day_week--;

      $out .= '<tr>';
      $out .= '<td class="calendar-head">' . $months[$month] . '</td>';
      // пустые ячейки
      for ($x = 0; $x < $day_week; $x++) {
        $out .= '<td class="empty"></td>';
      }

      $days_counter = 0;
      $days_month = date('t', mktime(0, 0, 0, $month, 1, $year));

      for ($day = 1; $day <= $days_month; $day++) {
        
        if (date('j.n.Y') == $day . '.' . $month . '.' . $year) {
          $class = 'today';
        } elseif (time() > strtotime($day . '.' . $month . '.' . $year)) {
          $class = 'last';
        } else {
          $class = '';
        }

        if ($day_week == 5) {
          $class = 'weekend';
          if (time() > strtotime($day . '.' . $month . '.' . $year)) {
            $class = 'last weekend';
          }
        }

        if ($day_week == 6) {
          $class = 'weekend';
          $day_week = -1;
          if (time() > strtotime($day . '.' . $month . '.' . $year)) {
            $class = 'last weekend';
          }
        }

        $event_show = false;
        $event_text = array();
        if (!empty($events)) {
          foreach ($events as $date => $text) {
            $date = explode('.', $date);
            if (count($date) == 3) {
              $y = explode(' ', $date[2]);
              if (count($y) == 2) {
                $date[2] = $y[0];
              }

              if ($day == intval($date[0]) && $month == intval($date[1]) && $year == $date[2]) {
                $event_show = true;
                $event_text[] = $text;
              }
            } elseif (count($date) == 2) {
              if ($day == intval($date[0]) && $month == intval($date[1])) {
                $event_show = true;
                $event_text[] = $text;
              }
            } elseif ($day == intval($date[0])) {
              $event_show = true;
              $event_text[] = $text;
            }
          }
        }

        if ($event_show) {
          $out .= '<td class="calendar-day ' . $class . ' event">' . $day;
          if (!empty($event_text)) {
            $out .= '<div class="calendar-popup">' . implode('<br>', $event_text) . '</div>';
          }
          $out .= '</td>';
        } else {
          $out .= '<td class="calendar-day ' . $class . '">' . $day . '</td>';
        }

        // выводить в виде календаря
        // if ($day_week == 6) {
        //   $out .= '</tr>';
        //   if (($days_counter + 1) != $days_month) {
        //     $out .= '<tr>';
        //   }
        //   $day_week = -1;
        // }

        $day_week++;
        $days_counter++;
      }

      $out .= '</tr></table></div>';
      return $out;
    }

    /**
     * Вывод календаря на несколько месяцев.
     */
    public static function  getInterval($start, $end, $events = array())
    {
      $curent = explode('.', $start);
      $curent[0] = intval($curent[0]);

      $end = explode('.', $end);
      $end[0] = intval($end[0]);

      $begin = true;
      $out = '<div class="calendar-wrp">';
      do {
        $out .= self::getMonth($curent[0], $curent[1], $events);

        if ($curent[0] == $end[0] && $curent[1] == $end[1]) {
          $begin = false;
        }

        $curent[0]++;
        if ($curent[0] == 13) {
          $curent[0] = 1;
          $curent[1]++;
        }
      } while ($begin == true);

      $out .= '</div>';
      return $out;
    }
  }

  $events = array(
    '16'    => '<a href="/">hello</a>',
    '23.02' => 'День защитника Отечества',
    '08.03' => 'Международный женский день',
    '31.12' => 'Новый год',
    '17.12' => 'Сдать поект'
  );

  //date('3.Y'), date('n.Y', strtotime(date('01.03.Y') . ' +3 month')) выведет март,апрель,май,июнь

  echo Calendar::getInterval(date('n.Y'), date('n.Y', strtotime('+11 month')), $events); ?>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="js/slick.min.js"></script>
  <script src="js/jquery.fancybox.min.js"></script>
  <script src="js/main.js"></script>
</body>

</html>