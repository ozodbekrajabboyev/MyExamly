<script src="https://www.gstatic.com/charts/loader.js"></script>

<div
    x-data="{ init: () => {
        // Wait until the google object is available
        let interval = setInterval(() => {
            if (window.google?.charts?.Bar) {
                clearInterval(interval)

                const data = google.visualization.arrayToDataTable([
                    ['Year', 'Sales', 'Expenses', 'Profit'],
                    ['2014', 1000, 400, 200],
                    ['2015', 1170, 460, 250],
                    ['2016', 660, 1120, 300],
                    ['2017', 1030, 540, 350]
                ]);

                const options = {
                    chart: {
                        title: 'Company Performance',
                        subtitle: 'Sales, Expenses, and Profit: 2014–2017',
                    },
                    bars: 'horizontal',
                };

                const chart = new google.charts.Bar(document.getElementById('barchart_material'));
                chart.draw(data, google.charts.Bar.convertOptions(options));
            }
        }, 100);
    }}"
    x-init="init"
>
    <div id="barchart_material" style="width: 100%; height: 400px;"></div>
</div>

<script>
    // Load Google Charts
    google.charts.load('current', { packages: ['bar'] });
    google.charts.setOnLoadCallback(() => {
        console.log('✅ Google Charts fully loaded');
    });
</script>
