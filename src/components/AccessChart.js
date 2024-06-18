import React, { Component } from 'react';
import Chart from 'chart.js/auto';
import CSVReader from 'react-csv-reader'; // Importa o CSVReader

class AccessChart extends Component {
  constructor(props) {
    super(props);
    this.state = {
      csvData: null,
      selectedYears: [],
      selectedMonths: [],
      chartInstance: null
    };
    this.chartRef = React.createRef();
    this.papaparseOptions = {
      header: true,
      dynamicTyping: true,
      skipEmptyLines: true,
      transformHeader: header =>
        header.toLowerCase().replace(/\W/g, '_')
    };
  }

  handleCSVData = (data) => {
    console.log(data);
    this.setState({ csvData: data }, this.createChart);
  }

  handleCSVError = (error) => {
    console.error(error);
  }

  handleYearChange = (event) => {
    const year = event.target.value;
    let selectedYears = [...this.state.selectedYears];
    if (selectedYears.includes(year)) {
      selectedYears = selectedYears.filter(y => y !== year);
    } else {
      selectedYears.push(year);
    }
    this.setState({ selectedYears }, this.updateChart);
  }
  
  handleMonthChange = (event) => {
    const month = event.target.value;
    let selectedMonths = [...this.state.selectedMonths];
    if (selectedMonths.includes(month)) {
      selectedMonths = selectedMonths.filter(m => m !== month);
    } else {
      selectedMonths.push(month);
    }
    this.setState({ selectedMonths }, this.updateChart);
  }
  
  filterData = () => {
    const { csvData, selectedYears, selectedMonths } = this.state;
    if (!csvData) return [];
    
    let filteredData = csvData;
    if (selectedYears.length > 0) {
      filteredData = filteredData.filter(item => selectedYears.includes(String(item.ano_de_acesso)));
    }
    if (selectedMonths.length > 0) {
      filteredData = filteredData.filter(item => selectedMonths.includes(String(item.m_s_de_acesso)));
    }
    return filteredData;
  }

  createChart = () => {
    const ctx = this.chartRef.current.getContext('2d');
    const filteredData = this.filterData();
    const labels = filteredData.map(item => `${item.ano_de_acesso}/${item.m_s_de_acesso}`);
    const data = filteredData.map(item => item.n_mero_de_acessos);

    if (this.state.chartInstance) {
      this.state.chartInstance.destroy();
    }

    const newChartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Número de Acessos',
          data: data,
          backgroundColor: ["red", "blue", "green", "orange", "purple", "black"],
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    this.setState({ chartInstance: newChartInstance });
  }

  updateChart = () => {
    if (this.state.csvData) {
      this.createChart();
    }
  }

  render() {
    const { csvData, selectedYears, selectedMonths } = this.state;
    const currentYear = new Date().getFullYear();

    return (
      <div className='App'>
      <div className="container">
        <h2>Total de Acessos ao Portal</h2>
        <CSVReader
          cssClass="csv-reader-input"
          label="Importe o arquivo CSV "
          onFileLoaded={this.handleCSVData}
          onError={this.handleCSVError}
          parserOptions={this.papaparseOptions}
          inputId="csvInput"
          inputName="csvInput"
          inputStyle={{ color: 'red' }}
        />
        <div>
          <label>
            Anos:
            <select multiple onChange={this.handleYearChange}>
              {Array.from({ length: currentYear - 1999 }, (_, i) => (2000 + i)).map(year => (
                <option key={year} value={year}>
                  {year}
                </option>
              ))}
            </select>
          </label>
          <label>
            Meses:
            <select multiple onChange={this.handleMonthChange}>
              {['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'].map(month => (
                <option key={month} value={month}>
                  {month}
                </option>
              ))}
            </select>
          </label>
        </div>
        <div className="chart-container">
          <canvas ref={this.chartRef} width={300} height={100}></canvas>
        </div>
      </div>
      </div>
    );
  }
}

export default AccessChart;
