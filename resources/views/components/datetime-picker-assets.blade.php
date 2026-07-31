<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.date-picker').forEach(function (input) { flatpickr(input, { dateFormat: 'Y-m-d', altInput: true, altFormat: 'D, d M Y', allowInput: true }); });
  document.querySelectorAll('.time-picker, input[name="start_time"], input[name="end_time"]').forEach(function (input) { flatpickr(input, { enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: false, allowInput: true }); });
});
</script>
