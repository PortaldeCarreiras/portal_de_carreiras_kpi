// src/components/AccessChart.js
import React, { useContext, useEffect, useRef, useState } from 'react';
import Chart from 'chart.js/auto';
import CSVReader from 'react-csv-reader';
import { CsvDataContext } from '../contexts/CsvDataContext';

const AccessChart = () => {
  const { csvData, updateCsvData } = useContext(CsvDataContext);
  const [selectedYears, setSelectedYears] = useState([]);
  const [selectedMonths, setSelectedMonths] = useState([]);
  const chartRef = useRef(null);
  const chartInstanceRef = useRef(null);

  useEffect(() => {
    if (csvData.length > 0) {
      createChart();
    }
  }, [csvData, selectedYears, selectedMonths]);

  const handleCSVData = (data) => {
    updateCsvData(data);
  };

  const handleCSVError = (error) => {
    console.error('Erro ao importar CSV:', error);
  };

  const handleYearChange = (event) => {
    const year = event.target.value;
    setSelectedYears((prev) =>
      prev.includes(year) ? prev.filter((y) => y !== year) : [...prev, year]
    );
  };

  const handleMonthChange = (event) => {
    const month = event.target.value;
    setSelectedMonths((prev) =>
      prev.includes(month) ? prev.filter((m) => m !== month) : [...prev, month]
    );
  };

  const filterData = () => {
    if (!csvData) return [];

    let filteredData = csvData;
    if (selectedYears.length > 0) {
      filteredData = filteredData.filter((item) =>
        selectedYears.includes(String(item.ano_de_acesso))
      );
    }
    if (selectedMonths.length > 0) {
      filteredData = filteredData.filter((item) =>
        selectedMonths.includes(String(item.m_s_de_acesso))
      );
    }
    return filteredData;
  };

  const createChart = () => {
    const ctx = chartRef.current.getContext('2d');
    const filteredData = filterData();
    const labels = filteredData.map(
      (item) => `${item.ano_de_acesso}/${item.m_s_de_acesso}`
    );
    const data = filteredData.map((item) => item.n_mero_de_acessos);

    if (chartInstanceRef.current) {
      chartInstanceRef.current.destroy();
    }

    chartInstanceRef.current = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Número de Acessos',
            data: data,
            backgroundColor: ['red', 'blue', 'green', 'orange', 'purple', 'black'],
            borderWidth: 1,
          },
        ],
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
          },
        },
      },
    });
  };

  const currentYear = new Date().getFullYear();

  return (
    <div className="App">
      <div className="container">
        <h2>Total de Acessos ao Portal</h2>
        <CSVReader
          cssClass="csv-reader-input"
          label="Importe o arquivo CSV "
          onFileLoaded={handleCSVData}
          onError={handleCSVError}
          parserOptions={{
            header: true,
            dynamicTyping: true,
            skipEmptyLines: true,
            transformHeader: (header) =>
              header.toLowerCase().replace(/\W/g, '_'),
          }}
          inputId="csvInput"
          inputName="csvInput"
          inputStyle={{ color: 'red' }}
        />
        <div>
          <label>
            Anos:
            <select multiple onChange={handleYearChange}>
              {Array.from({ length: currentYear - 1999 }, (_, i) => 2000 + i).map(
                (year) => (
                  <option key={year} value={year}>
                    {year}
                  </option>
                )
              )}
            </select>
          </label>
          <label>
            Meses:
            <select multiple onChange={handleMonthChange}>
              {['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'].map(
                (month) => (
                  <option key={month} value={month}>
                    {month}
                  </option>
                )
              )}
            </select>
          </label>
        </div>
        <div className="chart-container">
          <h2>Gráfico de Acesso ao Portal</h2>
          <canvas ref={chartRef} width={300} height={100}></canvas>
        </div>
      </div>
    </div>
  );
};

export default AccessChart;
