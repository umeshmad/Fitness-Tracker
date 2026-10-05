// Dashboard: weekly chart (data is passed from PHP as JSON) and the workout history filter.
(function () {
    var dataElement = document.getElementById('chartData');
    var canvas = document.getElementById('weekChart');
    if (dataElement && canvas && window.Chart) {
        var data = JSON.parse(dataElement.textContent);
        // The three values the metric buttons can switch between.
        var metrics = {
            calories: { label: 'Calories burned', values: data.calories, color: '#0b8f84' },
            steps: { label: 'Steps', values: data.steps, color: '#27546e' },
            minutes: { label: 'Minutes active', values: data.minutes, color: '#f5b800' }
        };
        var chart = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: metrics.calories.label,
                    data: metrics.calories.values,
                    backgroundColor: metrics.calories.color,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // The metric buttons swap the chart data in place instead of drawing a new chart.
        document.querySelectorAll('.metric-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                var metric = metrics[button.getAttribute('data-metric')];
                document.querySelectorAll('.metric-btn').forEach(function (other) {
                    other.classList.remove('active');
                    other.setAttribute('aria-pressed', 'false');
                });
                button.classList.add('active');
                button.setAttribute('aria-pressed', 'true');
                chart.data.datasets[0].label = metric.label;
                chart.data.datasets[0].data = metric.values;
                chart.data.datasets[0].backgroundColor = metric.color;
                chart.update();
            });
        });
    }

    // Filter the table in the browser by hiding rows that do not match the chosen activity.
    var filter = document.getElementById('activityFilter');
    var rows = document.querySelectorAll('#workoutRows tr[data-activity]');
    var emptyRow = document.getElementById('noMatchRow');
    if (filter && rows.length) {
        filter.addEventListener('change', function () {
            var choice = filter.value;
            var shown = 0;
            rows.forEach(function (row) {
                var match = choice === 'all' || row.getAttribute('data-activity') === choice;
                row.classList.toggle('d-none', !match);
                if (match) {
                    shown += 1;
                }
            });
            if (emptyRow) {
                emptyRow.classList.toggle('d-none', shown !== 0);
            }
        });
    }
})();
