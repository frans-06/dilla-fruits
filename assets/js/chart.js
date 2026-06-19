function initSalesTrendChart(elementId, labels, dataValues) {
  const ctx = document.getElementById(elementId);
  if (!ctx) return;

  return new Chart(ctx, {
    type: "bar",
    data: {
      labels: labels,
      datasets: [
        {
          label: "Omset Pendapatan (Rp)",
          data: dataValues,
          backgroundColor: "#16a34a",
          borderRadius: 8,
          borderSkipped: false,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: function (context) {
              return " " + formatRupiah(context.parsed.y);
            },
          },
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: { color: "#f3f4f6" },
          ticks: {
            callback: function (value) {
              return formatRupiah(value);
            },
          },
        },
        x: { grid: { display: false } },
      },
    },
  });
}
