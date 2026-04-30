<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grafik Data</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Grafik Data</h1>

    <canvas id="usersChart" width="400" height="200"></canvas>
    <canvas id="ordersChart" width="400" height="200"></canvas>
    <canvas id="productsChart" width="400" height="200"></canvas>

    <script>
        // Data for Users Chart
        const usersData = <?php echo json_encode(array_column($users, 'column_name')); ?>;
        const usersLabels = <?php echo json_encode(array_column($users, 'another_column_name')); ?>;

        const ctxUsers = document.getElementById('usersChart').getContext('2d');
        new Chart(ctxUsers, {
            type: 'bar',
            data: {
                labels: usersLabels,
                datasets: [{
                    label: 'Users Data',
                    data: usersData,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Repeat similar blocks for Orders and Products charts
    </script>
</body>
</html>