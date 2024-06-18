import React, { Component } from 'react';
import CSVReader from 'react-csv-reader';
import Chart from 'chart.js/auto';
import './Estilos.css';

class TotalEstagiandoChart extends Component {
  constructor(props) {
    super(props);
    this.state = {
      csvData: null,
      chartInstance: null,
      chartInstanceEixos: null,
      chartInstanceVagas: null,
      chartInstanceCourseSemesterTurn: null,
      companyCounts: {},
      totalStudents: 0,
      eixosCounts: {},
      vagasCounts: {},
      selectedYear: 'all',
      selectedSemester: 'all',
      selectedCourse: 'all',
      selectedTurn: 'all'
    };
    this.chartRef = React.createRef();
    this.chartEixosRef = React.createRef();
    this.chartVagasRef = React.createRef();
    this.chartCourseSemesterTurnRef = React.createRef();
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
    this.setState({ csvData: data }, this.createCharts);
  }

  handleCSVError = (error) => {
    console.error(error);
  }

  filterData = () => {
    const { csvData } = this.state;
    if (!csvData) return [];

    return csvData.filter(item => item.data_final && item.data_final.trim() !== '');
  }

  countStudentsByCompany = (filteredData) => {
    const companyCounts = {};
    let totalStudents = 0;

    filteredData.forEach(item => {
      const company = item.empresas_para_est_gio;
      if (company) {
        if (!companyCounts[company]) {
          companyCounts[company] = 0;
        }
        companyCounts[company]++;
        totalStudents++;
      }
    });

    return { companyCounts, totalStudents };
  }

  countStudentsByEixos = (filteredData) => {
    const eixosCounts = {
      Gestão: 0,
      Informática: 0,
      Aeronave: 0,
      'Gestão/Informática': 0,
      'Gestão/Aeronave': 0,
      'Informática/Aeronave': 0,
      'Gestão/Informática/Aeronave': 0
    };

    filteredData.forEach(item => {
      const aluno = item.aluno;
      if (aluno) {
        const subColumns = aluno.split(' - ');
        if (subColumns.length > 3) {
          const eixoCode = subColumns[3].trim();
          switch (eixoCode) {
            case '1':
              eixosCounts['Gestão']++;
              break;
            case '2':
              eixosCounts['Informática']++;
              break;
            case '3':
              eixosCounts['Aeronave']++;
              break;
            case '4':
              eixosCounts['Gestão/Informática']++;
              break;
            case '5':
              eixosCounts['Gestão/Aeronave']++;
              break;
            case '6':
              eixosCounts['Informática/Aeronave']++;
              break;
            case '7':
              eixosCounts['Gestão/Informática/Aeronave']++;
              break;
            default:
              break;
          }
        }
      }
    });

    return eixosCounts;
  }

  countVagasByCompany = (filteredData) => {
    const vagasCounts = {};

    filteredData.forEach(item => {
      const company = item.empresas_para_est_gio;
      if (company) {
        if (!vagasCounts[company]) {
          vagasCounts[company] = 0;
        }
        vagasCounts[company]++;
      }
    });

    return vagasCounts;
  }

  countStudentsByCourseSemesterTurn = (filteredData) => {
    const courseSemesterTurnCounts = {};

    filteredData.forEach(item => {
      const aluno = item.aluno;
      if (aluno) {
        const subColumns = aluno.split(' - ');
        if (subColumns.length > 1) {
          const courseCode = subColumns[1].slice(3, 6);
          const semesterCode = subColumns[1].slice(6, 9);
          const periodCode = subColumns[1].slice(9, 10);

          const course = {
            '048': 'Análise e Desenvolvimento de Sistemas',
            '028': 'Banco de Dados',
            '139': 'Desenvolvimento de Software Multiplataforma',
            '077': 'Gestão de Produção Industrial',
            '064': 'Gestão Empresarial',
            '074': 'Logística',
            '068': 'Manutenção de Aeronaves',
            '115': 'Projetos de Estruturas Aeronáuticas'
          }[courseCode];

          const year = '20' + semesterCode.slice(0, 2);
          const semester = semesterCode[2] === '1' ? '1º' : '2º';
          const period = periodCode === '1' ? 'Manhã' : (periodCode === '2' ? 'Tarde' : 'Noite');

          const key = `${course} - ${year}/${semester} - ${period}`;
          if (!courseSemesterTurnCounts[key]) {
            courseSemesterTurnCounts[key] = 0;
          }
          courseSemesterTurnCounts[key]++;
        }
      }
    });

    return courseSemesterTurnCounts;
  }

  createCharts = () => {
    const ctx = this.chartRef.current.getContext('2d');
    const ctxEixos = this.chartEixosRef.current.getContext('2d');
    const ctxVagas = this.chartVagasRef.current.getContext('2d');
    const ctxCourseSemesterTurn = this.chartCourseSemesterTurnRef.current.getContext('2d');

    const filteredData = this.filterData();
    const filteredDataByCriteria = this.filterDataByCriteria(filteredData);

    const { companyCounts, totalStudents } = this.countStudentsByCompany(filteredData);
    const eixosCounts = this.countStudentsByEixos(filteredData);
    const vagasCounts = this.countVagasByCompany(filteredData);
    const courseSemesterTurnCounts = this.countStudentsByCourseSemesterTurn(filteredDataByCriteria);

    const sortedCompanies = Object.entries(companyCounts).sort((a, b) => b[1] - a[1]);
    const labels = sortedCompanies.map(([company]) => company);
    const data = sortedCompanies.map(([, count]) => count);

    if (this.state.chartInstance) {
      this.state.chartInstance.destroy();
    }

    const newChartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Quantidade de Vagas por Empresa',
          data: data,
          backgroundColor: 'skyblue',
          borderWidth: 1,
          borderSkipped: 'middle',
          barThickness: 40,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            beginAtZero: true,
            autoSkip: false,
            maxRotation: 10,
            minRotation: 10
          },
          y: {
            beginAtZero: true
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });

    const eixosLabels = Object.keys(eixosCounts);
    const eixosData = Object.values(eixosCounts);

    if (this.state.chartInstanceEixos) {
      this.state.chartInstanceEixos.destroy();
    }

    const newChartInstanceEixos = new Chart(ctxEixos, {
      type: 'bar',
      data: {
        labels: eixosLabels,
        datasets: [{
          label: 'Quantidade de Alunos por Eixo',
          data: eixosData,
          backgroundColor: 'lightgreen',
          borderWidth: 1,
          barThickness: 50,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            beginAtZero: true,
            categoryPercentage: 0.8,
            barPercentage: 0.9,
          },
          y: {
            beginAtZero: true,
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });

    const sortedVagasCompanies = Object.entries(vagasCounts).sort((a, b) => b[1] - a[1]);
    const vagasLabels = sortedVagasCompanies.map(([company]) => company);
    const vagasData = sortedVagasCompanies.map(([, count]) => count);

    if (this.state.chartInstanceVagas) {
      this.state.chartInstanceVagas.destroy();
    }

    const newChartInstanceVagas = new Chart(ctxVagas, {
      type: 'bar',
      data: {
        labels: vagasLabels,
        datasets: [{
          label: 'Quantidade de Vagas por Empresa',
          data: vagasData,
          backgroundColor: 'lightcoral',
          borderWidth: 1,
          borderSkipped: 'middle',
          barThickness: 40,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            beginAtZero: true,
            autoSkip: false,
            maxRotation: 10,
            minRotation: 10
          },
          y: {
            beginAtZero: true
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });

    const courseSemesterTurnLabels = Object.keys(courseSemesterTurnCounts);
    const courseSemesterTurnData = Object.values(courseSemesterTurnCounts);

    if (this.state.chartInstanceCourseSemesterTurn) {
      this.state.chartInstanceCourseSemesterTurn.destroy();
    }

    const newChartInstanceCourseSemesterTurn = new Chart(ctxCourseSemesterTurn, {
      type: 'bar',
      data: {
        labels: courseSemesterTurnLabels,
        datasets: [{
          label: 'Alunos por Curso/Semestre/Turno',
          data: courseSemesterTurnData,
          backgroundColor: 'lightblue',
          borderWidth: 1,
          borderSkipped: 'middle',
          barThickness: 40,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            beginAtZero: true,
            autoSkip: false,
            maxRotation: 10,
            minRotation: 10
          },
          y: {
            beginAtZero: true
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });

    this.setState({
      chartInstance: newChartInstance,
      chartInstanceEixos: newChartInstanceEixos,
      chartInstanceVagas: newChartInstanceVagas,
      chartInstanceCourseSemesterTurn: newChartInstanceCourseSemesterTurn,
      companyCounts: companyCounts,
      totalStudents: totalStudents,
      eixosCounts: eixosCounts,
      vagasCounts: vagasCounts
    });
  }

  handleYearChange = (event) => {
    this.setState({ selectedYear: event.target.value }, this.createCharts);
  }

  handleSemesterChange = (event) => {
    this.setState({ selectedSemester: event.target.value }, this.createCharts);
  }

  handleCourseChange = (event) => {
    this.setState({ selectedCourse: event.target.value }, this.createCharts);
  }

  handleTurnChange = (event) => {
    this.setState({ selectedTurn: event.target.value }, this.createCharts);
  }

  filterDataByCriteria = (filteredData) => {
    const { selectedYear, selectedSemester, selectedCourse, selectedTurn } = this.state;
    return filteredData.filter(item => {
      const aluno = item.aluno;
      if (!aluno) return false;

      const subColumns = aluno.split(' - ');
      if (subColumns.length < 2) return false;

      const courseCode = subColumns[1].slice(3, 6);
      const semesterCode = subColumns[1].slice(6, 9);
      const periodCode = subColumns[1].slice(9, 10);

      const year = '20' + semesterCode.slice(0, 2);
      const semester = semesterCode[2] === '1' ? '1º' : '2º';
      const period = periodCode === '1' ? 'Manhã' : (periodCode === '2' ? 'Tarde' : 'Noite');

      const course = {
        '048': 'Análise e Desenvolvimento de Sistemas',
        '028': 'Banco de Dados',
        '139': 'Desenvolvimento de Software Multiplataforma',
        '077': 'Gestão de Produção Industrial',
        '064': 'Gestão Empresarial',
        '074': 'Logística',
        '068': 'Manutenção de Aeronaves',
        '115': 'Projetos de Estruturas Aeronáuticas'
      }[courseCode];

      return (selectedYear === 'all' || selectedYear === year) &&
             (selectedSemester === 'all' || selectedSemester === semester) &&
             (selectedCourse === 'all' || selectedCourse === course) &&
             (selectedTurn === 'all' || selectedTurn === period);
    });
  }

  render() {
    const { selectedYear, selectedSemester, selectedCourse, selectedTurn } = this.state;
    const yearOptions = [];
    const currentYear = new Date().getFullYear();
    for (let year = currentYear; year >= 2000; year--) {
      yearOptions.push(year.toString());
    }

    const semesterOptions = ['1º', '2º'];
    const courseOptions = [
      'Análise e Desenvolvimento de Sistemas',
      'Banco de Dados',
      'Desenvolvimento de Software Multiplataforma',
      'Gestão de Produção Industrial',
      'Gestão Empresarial',
      'Logística',
      'Manutenção de Aeronaves',
      'Projetos de Estruturas Aeronáuticas'
    ];
    const turnOptions = ['Manhã', 'Tarde', 'Noite'];

    return (
      <div className="App">
        <h2>Total de Alunos Estagiando</h2>
        <br></br>
        <CSVReader
          cssClass="csv-reader-input"
          label="Selecione um arquivo CSV"
          onFileLoaded={this.handleCSVData}
          onError={this.handleCSVError}
          parserOptions={this.papaparseOptions}
          inputId="ObiWan"
          inputStyle={{ color: 'red' }}
        />
        <div className="chart-container">
          <canvas ref={this.chartRef} className="chart" />
        </div>
        <div className="chart-container">
          <canvas ref={this.chartEixosRef} className="chart" />
        </div>
        <div className="chart-container">
          <canvas ref={this.chartVagasRef} className="chart" />
        </div>
        <div className="chart-container">
          <canvas ref={this.chartCourseSemesterTurnRef} className="chart" />
        </div>
        <div>
          <label>Ano:</label>
          <select value={selectedYear} onChange={this.handleYearChange}>
            <option value="all">Todos</option>
            {yearOptions.map(year => (
              <option key={year} value={year}>{year}</option>
            ))}
          </select>
        </div>
        <div>
          <label>Semestre:</label>
          <select value={selectedSemester} onChange={this.handleSemesterChange}>
            <option value="all">Todos</option>
            {semesterOptions.map(semester => (
              <option key={semester} value={semester}>{semester}</option>
            ))}
          </select>
        </div>
        <div>
          <label>Curso:</label>
          <select value={selectedCourse} onChange={this.handleCourseChange}>
            <option value="all">Todos</option>
            {courseOptions.map(course => (
              <option key={course} value={course}>{course}</option>
            ))}
          </select>
        </div>
        <div>
          <label>Turno:</label>
          <select value={selectedTurn} onChange={this.handleTurnChange}>
            <option value="all">Todos</option>
            {turnOptions.map(turn => (
              <option key={turn} value={turn}>{turn}</option>
            ))}
          </select>
        </div>
      </div>
    );
  }
}

export default TotalEstagiandoChart;
