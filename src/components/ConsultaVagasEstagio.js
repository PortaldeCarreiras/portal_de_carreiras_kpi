import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import CSVReader from 'react-csv-reader';
import Chart from 'chart.js/auto';
import 'chartjs-adapter-date-fns';
import './EstilosConsultaVagas.css'; // Importando o CSS
import './Estilos.css'

// Função para extrair ano e mês de uma data no formato DD/MM/AAAA
const extractYearMonth = (dateString) => {
  if (!dateString) return null; // Verifica se a data é nula
  const [day, month, year] = dateString.split('/');
  return `${year}-${month}`;
}

class ConsultaVagasEstagio extends Component {
  constructor(props) {
    super(props);
    this.state = {
      csvData: null,
      filteredData: [],
      chartInstance: null,
      filterType: 'all', // 'all', 'abertas', 'encerradas'
      selectedMonths: [],
      selectedYears: []
    };
    this.chartRef = React.createRef();
  }

  handleCSVData = (data) => {
    const filteredCSVData = data.filter(item => item.data_final_para_se_candidatar && item.data_final_para_se_candidatar.trim() !== '');
    console.log("Dados do arquivo CSV:", filteredCSVData);
    this.setState({ csvData: filteredCSVData }, this.filterData);
  }

  filterData = () => {
    const { csvData, filterType, selectedMonths, selectedYears } = this.state;
    if (!csvData) return;

    const currentDate = new Date();
    const filteredData = csvData.filter(item => {
      const dataFinal = extractYearMonth(item.data_final_para_se_candidatar);
      if (!dataFinal) return false; // Verifica se dataFinal é nula e retorna false
      if (filterType === 'abertas') {
        return dataFinal >= extractYearMonth(currentDate.toLocaleDateString());
      } else if (filterType === 'encerradas') {
        return dataFinal < extractYearMonth(currentDate.toLocaleDateString());
      }
      if (selectedMonths.length > 0 && !selectedMonths.includes(dataFinal.split("-")[1])) return false;
      if (selectedYears.length > 0 && !selectedYears.includes(dataFinal.split("-")[0])) return false;
      return true; // Retorna todas as vagas se nenhum filtro estiver selecionado
    });

    this.setState({ filteredData }, this.createChart);
  }

  handleFilterChange = (filterType) => {
    this.setState({ filterType }, this.filterData);
  }

  handleMonthChange = (month) => {
    const { selectedMonths } = this.state;
    const updatedMonths = selectedMonths.includes(month)
      ? selectedMonths.filter(m => m !== month)
      : [...selectedMonths, month];
    this.setState({ selectedMonths: updatedMonths }, this.filterData);
  }

  handleYearChange = (year) => {
    const { selectedYears } = this.state;
    const updatedYears = selectedYears.includes(year)
      ? selectedYears.filter(y => y !== year)
      : [...selectedYears, year];
    this.setState({ selectedYears: updatedYears }, this.filterData);
  }

  createChart = () => {
    const ctx = this.chartRef.current.getContext('2d');
    const { filteredData } = this.state;

    const labels = [...new Set(filteredData.map(item => extractYearMonth(item.data_final_para_se_candidatar)))];
    const data = labels.map(label => filteredData.filter(item => extractYearMonth(item.data_final_para_se_candidatar) === label).length);

    if (this.state.chartInstance) {
      this.state.chartInstance.destroy();
    }

    // Plugin para exibir os valores nas barras
    const valuePlugin = {
      id: 'valueDisplay',
      afterDatasetsDraw: function(chart) {
        const ctx = chart.ctx;
        chart.data.datasets.forEach((dataset, i) => {
          const meta = chart.getDatasetMeta(i);
          if (!meta.hidden) {
            meta.data.forEach((bar, index) => {
              const data = dataset.data[index];
              ctx.save();
              ctx.fillStyle = 'black';
              ctx.font = 'bold 12px Arial';
              ctx.textAlign = 'center';
              ctx.textBaseline = 'bottom';
              const yPos = bar.y - 5; // Ajuste para posicionar acima da barra
              ctx.fillText(data, bar.x, yPos);
              ctx.restore();
            });
          }
        });
      }
    };

    const newChartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Quantidade de Vagas',
          data: data,
          backgroundColor: ['blue', 'red'], // Azul para vagas abertas e vermelho para vagas encerradas
          borderWidth: 1
        }]
      },
      options: {
        plugins: {
          legend: {
            display: true,
            position: 'top',
          },
          valueDisplay: valuePlugin // Adiciona o plugin de exibição de valores
        },
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Quantidade de Vagas'
            }
          }
        }
      }
    });

    this.setState({ chartInstance: newChartInstance });
  }

  render() {
    const { filterType } = this.state;

    return (
      <div className="container">
        <CSVReader
          cssClass="csv-reader-input"
          label="Importe o arquivo CSV"
          onFileLoaded={this.handleCSVData}
          parserOptions={{
            header: true,
            dynamicTyping: true,
            skipEmptyLines: true,
            transformHeader: header =>
              header
                .toLowerCase()
                .replace(/\W/g, '_')
          }}
          inputId="csvInput"
          inputName="csvInput"
          inputStyle={{ color: 'red' }}
        />
        <div className="filter-container">
          <div className="filter">
            <button onClick={() => this.handleFilterChange('all')}>Todas as Vagas</button>
            <button onClick={() => this.handleFilterChange('abertas')}>Vagas Abertas</button>
            <button onClick={() => this.handleFilterChange('encerradas')}>Vagas Encerradas</button>
          </div>
        </div>
        <div className="filter-container">
          <div className="filter">
            <h2><b>Ano:</b></h2>
            <div className="checkbox-wrapper-7">
            {[...new Set((this.state.csvData || []).map(item => extractYearMonth(item.data_final_para_se_candidatar)?.split("-")[0]))]
        .filter(year => year)
        .sort((a, b) => parseInt(a) - parseInt(b)) // Ordena os anos numericamente
        .map(year => (
          <label key={year} className="checkbox-item">
            <input
              className="tgl tgl-ios"
              type="checkbox"
              checked={this.state.selectedYears.includes(year)}
              onChange={() => this.handleYearChange(year)}
            />
            {year}
          </label>
        ))}
            </div>
          </div>
          <div className="filter">
            <h2><b>Mês:</b></h2>
            <div className="checkbox-wrapper-7">
              {[...new Set(Array.from(this.state.csvData || []).map(item => extractYearMonth(item.data_final_para_se_candidatar)?.split("-")[1]))]
                .filter(month => month)
                .sort((a, b) => parseInt(a) - parseInt(b)) // Ordena os meses numericamente
                .map(month => (
                  <label key={month} className="checkbox-item">
                    <input
                      className="tgl tgl-ios"
                      type="checkbox"
                      checked={this.state.selectedMonths.includes(month)}
                      onChange={() => this.handleMonthChange(month)}
                    />
                    {month}
                  </label>
                ))}
            </div>
          </div>
        </div>
        <div className="chart-container">
          <canvas ref={this.chartRef} width={800} height={400}></canvas>
        </div>
      </div>
    );
  }
}

ReactDOM.render(<ConsultaVagasEstagio />, document.getElementById('root'));

export default ConsultaVagasEstagio;
