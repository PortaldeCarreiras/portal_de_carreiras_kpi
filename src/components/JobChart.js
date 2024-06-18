import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import CSVReader from 'react-csv-reader';
import Chart from 'chart.js/auto';
import './EstilosJobChart.css'; // Importando o arquivo CSS com os estilos

class App extends Component {
  constructor(props) {
    super(props);
    this.state = {
      csvData: null,
      selectedYears: [],
      selectedMonths: [],
      chartInstance: null // Armazenar a instância do gráfico
    };
    this.chartRef = React.createRef();
    // Opções de configuração para o react-csv-reader
    this.papaparseOptions = {
      header: true,
      dynamicTyping: true,
      skipEmptyLines: true,
      transformHeader: header =>
        header.toLowerCase().replace(/\W/g, '_')
    };
  }

  // Manipulador para os dados CSV carregados com sucesso
  handleCSVData = (data) => {
    console.log(data);
    this.setState({ csvData: data }, this.createChart);
  }

  // Manipulador para lidar com erros durante o carregamento do CSV
  handleCSVError = (error) => {
    console.error(error);
    // Lide com o erro aqui
  }

  // Manipulador para alterações na seleção do ano
  handleYearChange = (event) => {
    const year = event.target.value;
    let selectedYears = [...this.state.selectedYears];
    if (selectedYears.includes(year)) {
      selectedYears = selectedYears.filter(y => y !== year);
    } else {
      selectedYears.push(year);
    }
    this.setState({ selectedYears }, () => {
      this.updateChart(); // Atualiza o gráfico após a alteração nos anos selecionados
    });
  }
  
  handleMonthChange = (event) => {
    const month = event.target.value;
    let selectedMonths = [...this.state.selectedMonths];
    if (selectedMonths.includes(month)) {
      selectedMonths = selectedMonths.filter(m => m !== month);
    } else {
      selectedMonths.push(month);
    }
    this.setState({ selectedMonths }, () => {
      this.updateChart(); // Atualiza o gráfico após a alteração nos meses selecionados
    });
  }
  
  // Filtra os dados com base nos anos e meses selecionados
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

  // Cria o gráfico com base nos dados filtrados
  createChart = () => {
    const ctx = this.chartRef.current.getContext('2d');
    const filteredData = this.filterData();
    const labels = filteredData.map(item => `${item.ano_de_acesso}/${item.m_s_de_acesso}`);
    const data = filteredData.map(item => item.n_mero_de_acessos);

    // Destrói a instância anterior do gráfico, se existir
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
          backgroundColor: ["red", "blue", "green", "orange", "purple", "black"], // Cores dos gráficos
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

    // Atualiza a instância do gráfico no estado
    this.setState({ chartInstance: newChartInstance });
  }

  // Atualiza o gráfico quando há alterações nos filtros
  updateChart = () => {
    // Verifica se já há dados carregados antes de atualizar o gráfico
    if (this.state.csvData) {
      this.createChart();
    }
  }

  render() {
    const { csvData, selectedYears, selectedMonths } = this.state;
    const currentYear = new Date().getFullYear(); // Definindo o ano atual
    return (
      <div className='App'>
      <div className="container">
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
        {csvData && (
          <div className="filters-container">
            <div className="filter">
              <h2><b>Ano:</b></h2>
              <div className="checkbox-wrapper-7">
                {Array.from({ length: currentYear - 2021 }, (_, i) => {
                  const year = 2022 + i;
                  return (
                    <div key={year} className="checkbox-item">
                      <input
                        className="tgl tgl-ios"
                        id={`year-${year}`}
                        type="checkbox"
                        value={year}
                        checked={selectedYears.includes(String(year))}
                        onChange={this.handleYearChange}
                      />
                      <label className="tgl-btn" htmlFor={`year-${year}`}></label>
                      <span>{year}</span>
                    </div>
                  );
                })}
              </div>
            </div>
            <div className="filter">
              <h2><b>Mês:</b></h2>
              <div className="checkbox-wrapper-7">
                {[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].map(month => (
                  <div key={month} className="checkbox-item">
                    <input
                      className="tgl tgl-ios"
                      id={`month-${month}`}
                      type="checkbox"
                      value={month}
                      checked={selectedMonths.includes(String(month))}
                      onChange={this.handleMonthChange}
                    />
                    <label className="tgl-btn" htmlFor={`month-${month}`}></label>
                    <span>{month}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}
        <div className="chart-container">
          <canvas ref={this.chartRef} width={300} height={100}></canvas>
        </div>
      </div>
      </div>
    );
  }
}

// Renderiza o componente App na div com id 'root'
ReactDOM.render(<App />, document.getElementById('root'));
export default App;
