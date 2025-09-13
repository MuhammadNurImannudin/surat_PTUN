<?php
$title = 'Kalender – PTUN Banjarmasin';
require '../../config/database.php';
require '../../auth.php';
?>
<!doctype html>
<html lang="id">
<head><meta charset="UTF-8"><title><?= $title ?></title>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
</head>
<body>
<main class="container mt-4">
  <h4>Kalender PTUN</h4>
  <div id="calendar"></div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        events: [
          {title: 'Sidang Perkara #001', date: '2025-10-15', color: 'green'},
          {title: 'Deadline Surat', date: '2025-10-16', color: 'red'}
        ]
      });
      calendar.render();
    });
  </script>
</main>
</body>
</html>