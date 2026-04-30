document.addEventListener('DOMContentLoaded', function(){

    const ctx = document.getElementById('chart');

    if(ctx){

        const labels = window.chart_labels || [];
        const data   = window.chart_data || [];

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: data,
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true
            }
        });

    }

});